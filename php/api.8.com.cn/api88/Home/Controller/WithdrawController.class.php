<?php
/**
 * @file WithdrawController.class.php
 * @brief 用户提现控制器
 * 
 * Copyright (C) 2016 ZKC.com
 * All rights reserved.
 * 
 * @package Controller
 * 
 * $Id$
 */

namespace Home\Controller;

use Home\Controller\AppController;
use Home\Model\ProductsModel;
use Home\Model\CouponModel;
use Home\Model\UsersModel;
use Home\Model\WithDrawModel;

class WithdrawController extends AppController
{
	public function index() /* {{{ */
	{
		$token = self::$current_token;
		$uid = self::$current_uid;
		$appid = self::$current_appid;
	
		/** 获取用户信息 */
		$drawmodel = new WithDrawModel();
		$usermodel = new UsersModel();
		$userinfo = $usermodel->getinfo($uid);
		
		// 1、是否实名
		if ($userinfo['is_idcheck'] != 1) {
			return $this->_return(self::_error(441, '未实名认证'));
		}

		switch ($_SERVER['REQUEST_METHOD']) {
		case 'GET' :
			if (isset($_GET['ac']) && $_GET['ac'] == 'draw') {
				/** 用户点击提现 */
				$this->draw($userinfo);
			} else if (isset($_GET['ac']) && $_GET['ac'] == 'forget_tradepasswd') {
				/** 忘记交易密码 */
				$this->forget_tradepasswd($userinfo);
			}
			break;
		case 'POST' :
			if (isset($_POST['ac']) && $_POST['ac'] == 'validfee') {
				/** 计算手续费 */
				$this->validfee();
			} else if (isset($_POST['ac']) && $_POST['ac'] == 'checkpasswd') {
				/** 检测用户输入的交易密码 */
				$trade_password = I('post.trade_password','') + 0;
				$this->checkpasswd($trade_password, $userinfo);
			} else if (isset($_POST['ac']) && $_POST['ac'] == 'addcard') {
				/** 用户添加银行卡 */
				$this->add_userbank_card($userinfo);
			} else if (isset($_POST['ac']) && $_POST['ac'] == 'apply') {
				/** 确认，申请提现 */
				$this->apply($userinfo);
			}
			break;
		case 'PUT' :
			// (设置)更新交易密码
			$this->set_tradepwd($userinfo);
			break;
		case 'PATCH' :
			break;
		case 'DELETE' :
			break;
		default :
			break;
		}
		
		return $this->_return(self::_error(self::E_METHOD));
	} /* }}} */

	/**
	 * 用户提现操作
	 * 
	 * @access private
	 * @return string
	 */
	private function draw($userinfo)/*{{{*/
	{
		$token = self::$current_token;
		$uid = self::$current_uid;
		$appid = self::$current_appid;
		$data = array();

		/** 获取用户信息 */
		$drawmodel = new WithDrawModel();
		
		/*
		$usermodel = new UsersModel();
		$userinfo = $usermodel->getinfo($uid);
		// 1、是否实名
		if ($userinfo['is_idcheck'] != 1) {
			return $this->_return(self::_error(441, '未实名认证'));
		}*/

		// 2、判断用户是否有收益卡和易联卡
		$bank_result = $drawmodel->user_haved_bankinfo($uid);
		
		// 3、返回数据
		$realname = ''; /** 真实姓名 */
		$idcardno = ''; /** 身份证号码 */
		if (!$bank_result) { // 未添加银行卡(需要用户手动添加)
			$idcardno = substr($userinfo['idcardno'], 0, 2) . "**********" . substr($userinfo['idcardno'], -4, 4);
			$realname = $userinfo['realname'];
		}

		// 获取用户所有的银行卡(提现供用户选择)
		$draw_banks = $drawmodel->user_all_bankinfo($uid, $userinfo['ismzf']);
		// 计算银行卡可提现金额(同卡进出)
		foreach ($draw_banks as $key => $value) {
			$draw_banks[$key]['cardnumber'] = substr($value['cardnumber'], -4); // 截取银行卡后4位
			// 获取该卡可提现金额
			$valid_result = $this->validmoney_banks($value['id'], $value['cardstyle'], $userinfo);
			$draw_banks[$key]['valid_money'] = $valid_result['valid_money'];
		} /** END */
		
		/** 用户提现到账日，手续费 */
		$cash_money = 0;
		$cash_result = $drawmodel->cash_moneyresult($cash_money, $uid);

		$workingday = $cash_result['workingday'];
		$fee = number_format($cash_result['fee'], 2, ".", "");
		$nobuymoney = number_format($cash_result['nobuy_money'], 2, ".", ""); // 此值需保存到nobuymoney字段，
		$real_money = $cash_money - $data['fee'];
		$real_money = ($real_money < 0) ? 0.00 : number_format($real_money, 2, ".", "");
		
		$data = array(
			'realname' => $realname, /** 实名认证输入的姓名 */
			'idcardno' => $idcardno, /** 实名认证输入的身份证号码 */
			'pay_password' => $userinfo['paypassword'], /** 平台交易密码 */
			'my_money' => _undfzip($userinfo['mymoney']), /** 我的(账户)余额 */
			'cash_money' => $cash_money, // 默认提现金额 0
			'real_money' => $real_money, // 实际到账金额
			'workingday' => $workingday, // 提现到账工作日
			'fee' => $fee, // 提现手续费
			'nobuy_money' => $nobuymoney,
			'draw_banks' => $draw_banks, /** 提现银行卡 */
		);
		return $this->_return($data);
	}/*}}}*/

	/**
	 * 计算银行卡可以提现金额(同卡进出) 
	 * 
	 * @param int $band_cardid 银行卡id
	 * @access private
	 * @return array
	 */
	private function validmoney_banks($band_cardid, $card_style, $userinfo)/*{{{*/
	{
		$drawmodel = new WithDrawModel();
		$data = array(
			"valid_money" => 0.00, /** 该卡可提现金额 */
			"in_money" => 0.00,  /** 进平台金额 */
			"out_money" => 0.00, /** 出平台金额 */
			"is_yilian" => -1,
			"bank_money_info" => array(),
		);
		
		/** 卡类型，获取卡的信息 */
		$card_info = $this->get_card_info_by_style($card_style, $band_cardid, $userinfo['userid']);
		if (!$card_info) {
			return $this->_return(self::_error(452, '找不到银行卡信息'));
		}
		$bankinfo = $card_info['bankinfo'];
		$carnum = $card_info['carnum'];

		$bank_can_withdraw = 0.00; //本卡可体现资产
		$bank_can_not_withdraw = 0.00; //非本卡可体现资产，本卡不可体现资产
		$user_acount_money = floatval(_undfzip($userinfo['mymoney'])); // 平台账户余额资金
		$user_freeze_money = floatval(_undfzip($userinfo['freezemoney'])); // 平台冻结资金

		/** 获取该卡的信息 */
		$bank_money_info = $drawmodel->get_user_bank_money_info($userinfo['userid'], $carnum);
		if ($bank_money_info) {
			$bank_can_withdraw = $bank_money_info['amountfreeze']; // 冻结金额
			$data['in_money'] = number_format($bank_money_info['cardin'], 2, ".", ""); // 该卡总充值
			$data['out_money'] = number_format($bank_money_info['cardout'], 2, ".", ""); // 该卡总提现
			$data['is_yilian'] = $card_style;
			$data['bank_money_info'] = $bank_money_info;
		}

		// 获取用户冻结金额和总资产
		$money_result = $this->get_freezed_and_totalassets_by_userid($userinfo['userid']);

		// 最大可提现金额
		$valid_money = $money_result['total_assets'] - $money_result['freezed_money'] + $bank_can_withdraw;
		if ($valid_money > $user_acount_money) {
			$valid_money = $user_acount_money;
		}
		if ($valid_money < 0) {
			$valid_money = 0.00;
		}
		$valid_money = number_format($valid_money, 2, ".", "");
		$data['valid_money'] = $valid_money;

		return $data;
	}/*}}}*/

	/**
	 * 根据卡类型 获取卡信息 0：手动填写银行卡，1：易联充值卡，2：连连充值卡
	 * 
	 * @param int $cardstyle 卡类型
	 * @param int $band_cardid 银行卡id
	 * @param int $userid 用户id
	 * @return array 解密过的卡号 和 银行卡信息
	 */
	private function get_card_info_by_style($card_style, $band_cardid, $userid)/*{{{*/
	{
		$drawmodel = new WithDrawModel();
		
		if ($card_style == 0) { /** 用户手动填的提现银行卡 */
			$bankinfo = $drawmodel->get_bank_info($band_cardid, $userid);
			$carnum = _undfzip($bankinfo['cardnumber']);
			$info_txt = $bankinfo['cardaccount'].','.$bankinfo['cardtype'].','.$carnum.','.$bankinfo['cardbranch'];
		} else if ($card_style == 1) { /** 易联充值的银行卡 */
			$bankinfo = $drawmodel->get_yilian_bank_info($band_cardid, $userid);
			$carnum = _undfzip($bankinfo['bankcode']);
			$info_txt = $bankinfo['accountname'].','.$bankinfo['bankname'].','.$carnum.','.$bankinfo['branch'];
		} else if ($card_style == 2) { /** 连连充值的银行卡 */
			$bankinfo = $drawmodel->get_llbank_info($band_cardid, $userid);
			$carnum = $bankinfo['bankcode'];
			$info_txt = $bankinfo['accountname'].','.$bankinfo['bankname'].','.$carnum.','.$bankinfo['branch'];
		} else {
			return $this->_return(self::_error(440, '卡类型错误'));
		}
		
		if(!$bankinfo) {
			return false;
		}
		
		return array('carnum' => $carnum, 'bankinfo' => $bankinfo, 'info_txt' => $info_txt);
	}/*}}}*/

	/**
	 * 计算用户提现手续费, 实际提现金额，提现到账工作日 
	 * 
	 * @param float $cash_money 提现金额
	 * @access private
	 * @return string
	 */
	private function validfee($cash_money)/*{{{*/
	{
		// 用户提现金额
		$cash_money = I('post.cash_money', 0) + 0; // 提现金额
		$result = $this->cal_validfee($cash_money);
		return $this->_return($result);
	}/*}}}*/

	/**
	 * 计算用户提现手续费, 实际提现金额，提现到账工作日
	 * 
	 * @param type $cash_money
	 * @return array
	 */
	private function cal_validfee($cash_money)/*{{{*/
	{
		$uid = self::$current_uid;
		$drawmodel = new WithDrawModel();
		$usermodel = new UsersModel();
		$userinfo = $usermodel->getinfo($uid);

		$my_money = _undfzip($userinfo['mymoney']); /** 用户余额 */
		$data = array(
			"fee" => "0.00", /** 手续费 */
			"cash_money" => "0.00", /** 提现金额 */
			"real_money" => '0.00', /** 实际到账金额：提现金额-手续费 */
			"workingday" => '0', /** 多少工作日到账 */
			'nobuy_money' => '0.00' /** 充值未认购产品金额 */
		);
		$data['cash_money'] = $cash_money;
		/** 用户提现到账日，手续费 */
		$cash_result = $drawmodel->cash_moneyresult($cash_money, $uid);
		/** 判断输入数据是否合法 */
		if ($cash_result['fee'] == 1) {
			if ($cash_money < 2 || $cash_money > $my_money) {
				return $this->_return(self::_error(442, '提现金额错误或者超过范围1...'));
			}
		} else {
			if ($cash_money <= 0 || $cash_money > $my_money) {
				return $this->_return(self::_error(442, '提现金额错误或者超过范围2...'));
			}
		}

		$data['workingday'] = $cash_result['workingday'];
		$data['fee'] = number_format($cash_result['fee'], 2, ".", "");
		$data['nobuy_money'] = number_format($cash_result['nobuy_money'], 2, ".", ""); // 此值需保存到nobuymoney字段，
		$real_money = $cash_money - $data['fee'];
		$data['real_money'] = ($real_money < 0) ? 0.00 : number_format($real_money, 2, ".", "");
		
		return $data;
	}/*}}}*/
	
	/**
	 * 检测用户输入的交易密码
	 * 
	 * @param string $trade_password 交易密码(明文)
	 * @param array $userinfo 用户信息
	 * @access private
	 * @return string
	 */
	private function checkpasswd($trade_password, $userinfo)/*{{{*/
	{
		$pwd_length = strlen($trade_password);

		if (empty($trade_password) || $trade_password < 0|| $pwd_length > 7) {
			return $this->_return(self::_error(443, '交易密码错误1...'));
		}
		if ($pwd_length > 0 && $pwd_length < 6) {
			return $this->_return(self::_error(444, '交易密码小于6位'));
		}
		if (md5(md5($trade_password)) != $userinfo['paypassword']) {
			return $this->_return(self::_error(443, '交易密码错误2...'));
		}

		return $this->_return(self::_error(self::E_OK, 'OK'));
	}/*}}}*/

	/**
	 * 用户提现设置/修改交易密码(用户中心设置-通用)
	 * 
	 * @param array $userinfo
	 * @return string json 数据
	 */
	private function set_tradepwd($userinfo)/*{{{*/
	{
		/** 原始交易密码 交易密码(明文)(6位数字) */
		$trade_password = I('post.trade_password', '') + 0;
		$new_trade_password = I('post.new_trade_password', '') + 0; // 新交易密码
		$r_trade_password = I('post.r_trade_password', '') + 0; // 重复(新)交易密码

		$result = $this->_edit_trade_password($trade_password, $r_trade_password, $userinfo, $new_trade_password);
		if ($result !== false) {
			return $this->_return(self::_error(self::E_OK, 'OK'));
		}
		
		return $this->_return(self::_error(460, '设置失败'));
	}/*}}}*/
	
	/**
	 * 校验交易密码并且入库 (设置交易密码, 修改交易密码, 忘记交易密码 通用)
	 * 
	 * @param int $trade_password 交易密码
	 * @param int $r_trade_password 重复密码
	 * @param array $userinfo 用户数据
	 * @param int $new_trade_password 新交易密码(修改密码必须*) 默认空串
	 * @param int $flag 标记是哪个动作修改
	 * @return boolean
	 */
	private function _edit_trade_password($trade_password, $r_trade_password, /*{{{*/
			$userinfo, $new_trade_password='', $flag='')
	{
		$usermodel = new UsersModel();
		if ($trade_password <= 0 || strlen($trade_password) != 6) {
			return $this->_return(self::_error(458, '交易密码必须为6位数字'));
		}
		
		if (!empty($new_trade_password)) { // 修改交易密码	
			if (md5(md5($trade_password)) != $userinfo['paypassword']  && !empty($userinfo['paypassword'])) {
				transaction_password($userinfo['userid'], TRUE);
				return $this->_return(self::_error(464, '原交易密码错误'));
			}
			if (md5(md5($new_trade_password)) == $userinfo['paypassword']  && !empty($userinfo['paypassword'])) {
				return $this->_return(self::_error(465, '新旧密码相同无需修改'));
			}
			if ($new_trade_password <= 0 || strlen($new_trade_password) != 6) {
				return $this->_return(self::_error(461, '新密码重复交易密码必须为6位数字'));
			}		
			if ($r_trade_password <= 0 || strlen($r_trade_password) != 6) {
				return $this->_return(self::_error(458, '重复交易密码必须为6位数字'));
			}
			if ($new_trade_password != $r_trade_password) {
				return $this->_return(self::_error(459, '两次密码输入不一致'));
			}
			// 旧密码错误次数
			$trpwd_count = transaction_password($userinfo['userid']);
			if (empty($trpwd_count)) {
				return $this->_return(self::_error(463, '校验交易密码失败次数过多，请稍后再试!'));
			}
			
			// 更新字段数据
			$data['payPassWord'] = md5(md5($new_trade_password));			
		} else { // 设置交易密码
			if ($flag === 'getpwd2') { // 找回交易密码方式
				// 不做处理
			} else { // 设置交易密码
				if ($userinfo['paypassword']) {
					return $this->_return(self::_error(466, '已设置交易密码'));
				}
			}
			
			if ($r_trade_password <= 0 || strlen($r_trade_password) != 6) {
				return $this->_return(self::_error(458, '重复交易密码必须为6位数字'));
			}
			if ($trade_password != $r_trade_password) {
				return $this->_return(self::_error(459, '两次密码输入不一致'));
			}
			// 更新字段数据
			$data['payPassWord'] = md5(md5($trade_password));
		}
		
		if ($data['payPassWord'] == $userinfo['password']) {
			return $this->_return(self::_error(462, '请不要设置与登录密码一致的交易密码'));
		}
		// 入库
		$result = $usermodel->set_tradepwd($userinfo['userid'], $data);
		return $result;
	}/*}}}*/

	/**
	 * 用户确认，申请提现 
	 * 
	 * @param array $userinfo 用户基本信息
	 * @access private
	 * @return string
	 */
	private function apply($userinfo)/*{{{*/
	{
		$drawmodel = new WithDrawModel();
		
		// 参数处理
		$bank_id = I('post.bank_id', 0) + 0; // 银行卡id
		$cardstyle = I('post.cardstyle', -1); //  0:手填银行卡，1：易联卡 2：连连充值卡
		$cardstyle = ($cardstyle === '') ? -1 : $cardstyle;
		$valid_money = I('post.valid_money', 0) + 0; // 可提现金额
		$cash_money = I('post.cash_money', 0) + 0; // 提现金额
		$fee = I('post.fee', 0) + 0; // 提现手续费
		$real_money = I('post.real_money', 0) + 0; // 真实提现金额 = 提现金额-手续费
		$trade_password = I('post.trade_password', ''); // 交易密码(明文)(6位数字)
		$add_card_and_cash = I('post.addcardandcash', ''); // 同时增加卡和提现
		
		/** 密码错误判断 */
		if (!preg_match("/^\d{6}$/", $trade_password) || 
				md5(md5($trade_password)) != $userinfo['paypassword']) {
			return $this->_return(self::_error(443, '交易密码错误3...'));
		}
		
		// 用户未有提现银行卡，填写银行卡信息并且提现
		if ($add_card_and_cash) {
			// 添加银行卡入库
			$bank_result = $this->_add_userbank($userinfo);
			if ($bank_result) {
				$bank_id = $bank_result;
				$cardstyle = 0;
				// 获取用户冻结金额和总资产
				$money_result = $this->get_freezed_and_totalassets_by_userid($userinfo['userid']);
				// 最大可提现金额
				$valid_money = $money_result['total_assets'] - $money_result['freezed_money'];
			} else {
				return $this->_return(self::_error(448, '银行卡添加失败'));
			}
				
		} /** end if*/	
		
		// 参数错误提示 -- 判断输入数据是否合法
		if ($bank_id <= 0) {
			return $this->_return(self::_error(449, '银行卡id错误'));
		}
		if (!is_numeric($cardstyle) || !in_array($cardstyle, array(0, 1, 2))) {
			return $this->_return(self::_error(457, '银行卡类型错误'));
		}
		
		// 查询用户，到账日，未购买金额，手续费
		$result_validfee = $this->cal_validfee($cash_money);
		$fee_money = $result_validfee['fee']; // 提现手续费
		$workingday = $result_validfee['workingday']; // 如果返回值等于7，提示7个工作日才会到账
		$no_buy_money = $result_validfee['nobuy_money']; // 此值需保存到nobuymoney字段
		if ($workingday == 7) {
			$do_working_day = strtotime(getworkendday(date('Y-m-d'), 7, 1)); // 7个工作日后的日期，需保存
		}
		
		if ($fee != $fee_money) {
			return $this->_return(self::_error(450, '前台费用和后台费用不一致'));
		}
		
		if ($real_money <= 0 || $real_money != ($cash_money - $fee_money)) {
			return $this->_return(self::_error(451, '实际提现金额错误'));
		}

		// 获取银行卡信息
		$card_info = $this->get_card_info_by_style($cardstyle, $bank_id, $userinfo['userid']);
		if (!$card_info) {
			return $this->_return(self::_error(452, '找不到银行卡信息'));
		}
	
		$bankinfo = $card_info['bankinfo'];
		$carnum = $card_info['carnum']; // 已经解密过的卡号
		$info_txt = $card_info['info_txt'];

		$user_acount_money = floatval(_undfzip($userinfo['mymoney'])); // 平台账户余额资金
		$user_freeze_money = floatval(_undfzip($userinfo['freezemoney'])); // 平台冻结资金
		
		if ($user_freeze_money > 0) {
			// 有冻结金额标示用户正在提现 (上一笔提现未完成)
			return $this->_return(self::_error(453, '有未完成的提现1...'));
		} else {
			if ($drawmodel->is_withdraw_doing($userinfo['userid'])) {
				return $this->_return(self::_error(453, '有未完成的提现2...'));
			}
		}
		
		// 判断银行卡记录表中是否有提现(log_data)
		if ($drawmodel->is_cashing($userinfo['userid'])) {
			return $this->_return(self::_error(454, '银行卡有未完成的提现...'));
		}
		
		// 获取银行卡最大提现金额
		$sys_valid_money_result = $this->validmoney_banks($bank_id, $cardstyle, $userinfo);
		$sys_valid_money = $sys_valid_money_result['valid_money'];

//		if ($valid_money > $user_acount_money) {
//			$valid_money = $user_acount_money;
//		}
//				echo 'sys='.$sys_valid_money,'u='.$valid_money;exit;
		if ($valid_money != $user_acount_money) {
			return $this->_return(self::_error(455, '提现金额错误1...'));
		}
		if ($valid_money < 0) {
			$valid_money = 0.00;
		}
		/** 判断可提现金额 */
		if ($sys_valid_money != number_format($valid_money, 2, ".", "") ||
				$cash_money > $sys_valid_money) {
			return $this->_return(self::_error(455, '提现金额错误'));
		}
		
		// 计算此卡 有多少本金可以提现  有多少收益可以体现
		$out_money = 0.00; // 此卡可提现本金
		$extra_money = 0.00; // 此刻可体现收益
		$out_money = $cash_money;
		if ($sys_valid_money_result['bank_money_info']) {
			$bank_info_log = $sys_valid_money_result['bank_money_info'];
			// 提现金额超出当前卡的amountfreeze
			if ($cash_money - $bank_info_log['amountfreeze'] > 0) {
				$extra_money = $cash_money - $bank_info_log['amountfreeze'];
				$out_money = $bank_info_log['amountfreeze'];
			}
		}
		// 开始提现
		// TODO: 后期添加 $from_os => $appid
		if ($userinfo['ismzf'] == 0) { // 募资方 0不是，1是借款方
			$draw_result = $drawmodel->do_action_cash($userinfo['userid'], $real_money,
					$cash_money, $bank_id, $user_acount_money, $user_freeze_money,
					$do_working_day, $no_buy_money, $cardstyle, $info_txt,
					$carnum, $out_money, $extra_money, 1);
			
			// 更改冻结金额表(减去提现金额)
			try {
				$resutl = $drawmodel->change_bank_money($userinfo['userid'], $carnum, "CASH_OUT", $out_money);
			} catch (Exception $exc) {
				// 出现异常：未修改银行卡信息
				$date = date("Y-m-d H:i:s", time());
				$info = "{$date}-{$userinfo['userid']}-$carnum-CASH_OUT-$out_money";
				$exc_info = "NO UPDATE data_bankmoney table OF log_data DATABASE";
				wlog('api-withdraw-exception.txt', "{$info}: ".$exc_info." ".$_SERVER['REQUEST_URI']);
				;
			}
		} else {
			$draw_result = $drawmodel->do_action_cash_mzf($userinfo['userid'],
					$real_money, $bank_id, $user_acount_money, $user_freeze_money);
		}
		
		if ($draw_result) {
			return $this->_return(self::_error(self::E_OK, 'OK'));
		}
		return $this->_return(self::_error(456, '提现失败'));
	}/*}}}*/
	
	/**
	 * 添加银行卡
	 * 
	 * @param array $userinfo 用户id
	 * @return int
	 */
	private function add_userbank_card($userinfo)/*{{{*/
	{	
		$bank_result = $this->_add_userbank($userinfo);
		if (!$bank_result) {
			return $this->_return(self::_error(448, '银行卡添加失败'));
		}
		return $this->_return(self::_error(self::E_OK, 'OK'));
	}/*}}}*/
	
	/**
	 * 添加银行卡入库(提现同时设置, 用户中心设置 - 通用)
	 *
	 * @param array $userinfo 用户信息
	 * @return int
	 */
	private function _add_userbank($userinfo)/*{{{*/
	{
		$drawmodel = new WithDrawModel();
		
		// 查询是否添加了提现银行卡
		$bank_info = $drawmodel->get_user_bank($userinfo['userid']);
		if ($bank_info) { // 本平台实行同卡进出规则,目前仅能手动添加一张提现银行卡
			return $this->_return(self::_error(445, '已有银行卡'));
		}

		$card_type = I('post.card_type', ''); // 开户银行卡
		$card_number = I('post.card_number', 0); // 卡号
		$card_branch = I('post.card_branch', ''); // 支行信息
		$prov = I('post.prov', ''); // 省份
		$city = I('post.city', ''); // 城市
		$card_account = $userinfo['realname']; // 用户真实姓名
		if (empty($card_type) || empty($card_number) || empty($card_branch)
		|| empty($prov) || empty($city)) {
			return $this->_return(self::_error(446, '银行卡信息不完整'));
		}

		// 组装数据
		$data_arr['userid'] = $userinfo['userid'];
		$data_arr['prov'] = $prov;
		$data_arr['city'] = $city;
		$data_arr['cardType'] = $card_type; // 开户行
		$data_arr['cardBranch'] = $card_branch; // 开户支行
		$data_arr['cardNumber'] = _dfzip($card_number); // 卡号
		$data_arr['cardAccount'] = $card_account; // 姓名
		$data_arr['hash'] = md5(md5($card_type.$data_arr['cardNumber'].$card_account));

		if ($drawmodel->is_exist_bankcard_num($data_arr['cardNumber'])) {
			return $this->_return(self::_error(447, '银行卡已经被占用'));
		}

		// 添加到数据库
		$bank_result = $drawmodel->add_userbank($data_arr);
		return $bank_result;
	}/*}}}*/

	/**
	 * 获取用户的冻结金额， 用户总资产 
	 * 
	 * @param int $userid 
	 * @access private
	 * @return array
	 */
	private function get_freezed_and_totalassets_by_userid($userid)/*{{{*/
	{
		$drawmodel = new WithDrawModel();
		$bankfreezed_money = 0.00;
		$user_total_money = 0.00;

		// 获取用户冻结金额
		$bankfreezed_money = $drawmodel->get_all_user_bankfreezed_money($userid);
		// 用户总资产
		$user_total_money = $drawmodel->get_user_total_assets($userid);

		if ($userid == 16177) { /** ?? 不知道什么用户 ?? */
			$bankfreezed_money = 0;
		}

		return array(
			'freezed_money' => $bankfreezed_money, // 冻结金额
			'total_assets' => $user_total_money, // 用户总资产
		);
	}/*}}}*/
	
	/**
	 * 忘记交易密码
	 *
	 * @param array $userinfo 用户基本信息
	 */
	private function forget_tradepasswd($userinfo)/*{{{*/
	{
		$steps = I('steps', 1); // 步骤
		$redis = $this->data_store->FRedis;
		$usermodel = new UsersModel();
		$getpwd2_key_prefix = "getpwd2:{$userinfo['userid']}:";
		$expire_time = 1800; // 操作时间: 秒
		
		switch ($steps) {
			case 1:
				$username = I('username', '');
				$mobile = I('mobile', '');
				$verify_token = I('verify_token', ''); // 图形验证码token
				if (empty($username) || empty($mobile) || empty($verify_token)) {
					return $this->_return(self::_error(468, '信息不完整'));
				}
				if (!preg_match("/^(13|15|18|14|17)[0-9]{9}$/", $mobile)) {
					return $this->_return(self::_error(469, '手机号非法！'));
				}
				/* image verify code */
				if (!empty($verify_token)) {
					$image_code = I('image_code'); // 图形验证码
					$key_image = KEY_PREFIX.':verify_code:'.$verify_token;
					$v = $redis->hGetAll($key_image);
					if (empty($v) || empty($image_code) || strtolower($image_code) != strtolower($v['code'])) {
						return $this->_return(self::_error(470, '图形验证码错误！'));
					}
					$redis->del($key_image);
				}
				// 用户检测
				$user_result = $usermodel->get_user_by_usernameoremail($username);
				$user_mobile = _undfzip($user_result['dfmobile']);
				if ($mobile != $user_mobile) {
					return $this->_return(self::_error(471, '用户手机不匹配！'));
				}
				// 设置hash散列值进行下一步验证
				$hash = md5($mobile.$userinfo['userid'].time().'hashzkc');
				// getpwd2:uid:1, 1800, $hash
				$getpwd2_key = $getpwd2_key_prefix . $steps; // 第一步的key
				$hash_result = $redis->setex($getpwd2_key, $expire_time, $hash);
				
				// 返回成功后前端调用发送找回密码验证码
				if ($hash_result) {
					return $this->_return(self::_error(self::E_OK, 'OK', array('steps'=>2,'hash'=>$hash)));
				}
				
				return $this->_return(self::_error(473, '系统出错，请稍后再试！'));
				break;
			case 2:
				$mobile_code = I('mobile_code', ''); //  短信验证码
				$hash = I('hash', ''); //  hash验证
				$tel = _undfzip($userinfo['dfmobile']);
				if (empty($mobile_code) || empty($hash)) {
					return $this->_return(self::_error(468, '信息不完整'));
				}
				/* register verify code */
				$key = KEY_PREFIX.':verify_code:getpwd2:'.$tel;
				$v = $redis->hGetAll($key);
				if (empty($v) || empty($mobile_code) || $mobile_code != $v['code']) {
					return $this->_return(self::_error(472, '手机验证码错误！'));
				}
				// hash验证
				$check_steps = $steps - 1;
				$getpwd2_key_check = $getpwd2_key_prefix . $check_steps; // 第一步设置的key
				$is_timeout = $this->is_tiemout_for_getpwd2($getpwd2_key_check, $hash);
				if ($is_timeout) {
					return $this->_return(self::_error(474, '操作超时', array('steps'=>1))); // 返回第一步 重新提交
				}
				$getpwd2_key = $getpwd2_key_prefix . $steps;
				$redis->rename($getpwd2_key_check, $getpwd2_key); // key重新命名

				return $this->_return(self::_error(self::E_OK, 'OK', array('steps'=>3, 'hash'=>$hash)));
				break;
			case 3:
				$hash = I('hash', ''); //  hash验证
				$trade_password = I('trade_password', '') + 0;
				$r_trade_password = I('r_trade_password', '') + 0; // 重复交易密码
				if (empty($hash)) {
					return $this->_return(self::_error(468, '信息不完整'));
				}
				// hash验证
				$check_steps = $steps - 1;
				$getpwd2_key_check = $getpwd2_key_prefix . $check_steps; // 第二步设置的key
				$is_timeout = $this->is_tiemout_for_getpwd2($getpwd2_key_check, $hash);
				if ($is_timeout) {
					return $this->_return(self::_error(474, '操作超时', array('steps'=>1))); // 返回第一步 重新提交
				}
				// 数据验证并且进行更新
				$result = $this->_edit_trade_password($trade_password, $r_trade_password, $userinfo, '', 'getpwd2');
				if ($result !== false) {
					$redis->delete($getpwd2_key_check); // 删除hash验证
					return $this->_return(self::_error(self::E_OK, 'OK', array('steps'=>4)));
				}
				return $this->_return(self::_error(460, '设置失败'));
				break;
			default:
				return $this->_return(self::_error(467, '步骤出错'));
				break;
			}
	}/*}}}*/
	
	/**
	 * 判断忘记交易密码操作是否超时
	 * 
	 * @param int $userid 用户id
	 * @param int $value 要对比的值(hash)
	 * @return boolean true(超时)/false(正常)
	 */
	private function is_tiemout_for_getpwd2($key, $value)/*{{{*/
	{
		$redis = $this->data_store->FRedis;
		
		$ttl_result = $redis->ttl($key); // 获取操作剩余时间
		$getpwd2_value = $redis->get($key); // 获取hash值
		if ($ttl_result == -2 || empty($getpwd2_value) 
				|| ($value != $getpwd2_value)) {
			return true;
		}
		return false;
	}/*}}}*/

}

/* end file */
