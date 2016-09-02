<?php
/**
 * @file LicaiPlanController.class.php
 * @brief 订单相关-理财计划
 * 
 * Copyright (C) 2016 ZKC.com
 * All rights reserved.
 * 
 * @package Controller
 * 
 * $Id$
 */

namespace Home\Controller;

use Think\Controller; // 测试
use Home\Controller\AppController;
use Home\Model\OrdersModel;
use Home\Model\BillsModel;
use Home\Model\OrdersBillViewModel;
use Home\Model\ProductXzbModel;
use Home\Model\CouponModel;
use Home\Model\CoinsModel;
use Home\Model\ProductSxbModel;

class LicaiplanController  extends AppController 
//class LicaiplanController extends Controller 
{
	/**
	 * @brief 用户理财计划首页展示数据
	 * @param 
	 *!@param appid
	 * @return 
	 * {
	 * 	"orders":[{},{}],
	 * }
	 */
	public function index() /* {{{ */
	{
		/* 当前授权用户信息： */
		/*
		 *$token = self::$current_token;
		 *$uid = self::$current_uid;
		 *$appid = self::$current_appid;
		 */

		/* 客户端传递的参数在：$this->args; */
		/* TODO: ... */
		//p(D('ProductXzb'));exit;
		// http://api.88.com.cn/licaiPlan
		//$userid = $map['userid'] = 204865;
		$userid = self::$current_uid;

		$xzb_model = D('ProductXzb');
		// 用户计划数
		$periodNumber = $xzb_model->getPeriodNumber($userid);
		// 省心宝数据统计
		$sxb_modle = D('ProductSxb');
		$jinqu = $sxb_modle->getOrdersRows($userid, 1);
		$wenjian = $sxb_modle->getOrdersRows($userid, 2);
		$data = array(
			'xzb' => count($periodNumber),
			'jinqu' => $jinqu,
			'wenjian' => $wenjian
		);
		//echo json_encode($data);exit;
		//return $this->_return($data);
		return $this->_return(self::_error(self::E_OK, '', $data));
	} /* }}} */
	
	
	/**
	 * 理财计划详情（薪资宝，省心宝）
	 * 
	 * GET 请求
	 * 参数：
	 * type 1:薪资宝 2：省心宝
	 * typeid 薪资宝：period_number 的值，省心宝：poid 的值
	 * 
	 * @access public
	 * @return array
	 */
	public function detail()/*{{{*/
	{
		//$uid = $map['userid'] = 204865; // 测试
		$userid = self::$current_uid;
		/* 客户端传递的参数：I(''); */
		$type = I('type') + 0;
		$typeid = I('typeid') + 0;
		
		if ($type == 1) { // 新资宝
			$this->_detail_xzb($typeid, $userid);
		} elseif ($type == 2) { // 省心宝
			//echo '省心宝';
			$this->_detail_sxb($typeid, $userid);
		} else {
			/** TODO */
			//exit('参数错误');
			return $this->_return(self::_error(440, 'type,typeid is null or error'));
		}
	}/*}}}*/
	
	/**
	 * 省心宝订单详情
	 * http://api.88.com.cn/licaiPlan/detail?type=2&typeid=440
	 * 
	 * @param mixed $typeid 类型:2
	 * @param mixed $userid 用户id
	 * @access private
	 * @return void
	 */
	private function _detail_sxb($typeid, $userid)/*{{{*/
	{
		//echo $typeid,'--', $userid;
		$data = array();
		$sxb_modle = D('ProductSxb');
		$curentTime = time();
		$sxblist = $sxb_modle->getSxbOrders($userid);
		//p($sxblist);
		// 省心宝明细
		$sxborderinfo = array();
		$id = empty($typeid) ? 0 : $typeid;
		if(empty($id)){
			$id = $sxblist[0]['id'];
		}
		$data['typeid'] = $id; // 省心宝计划id
		$data['sxblist'] = $sxblist; // 用户所有的（省心宝列表）
		if($id){
			foreach ($sxblist as $v){
				if($id == $v['id']){
					$sxborderinfo = $v;break; 
				}
			}
			//p($sxborderinfo);
			// 获取季盈宝和专定盈订单
			$jyb = array();
			$jyblist = array();
			$zdy = array();
			if($sxborderinfo['coid1']){ // 子认购订单ID1-复利
				$jyb = $sxb_modle->getOrderBillById($sxborderinfo['coid1']); //季赢宝
				// 总期数
				$jyb['maxqx'] = 5;
				// 离下一期还有几天
				$jyb['laveday'] = intval(ceil($jyb['compoundtime'] - $curentTime) / 86400);
				// 获取产品使用88币
				$productCoins = D('Coins')->getProductCoins($userid, $jyb['ordernum']);
				$productCoins = empty($productCoins) ? 0 : $productCoins['tradeNum'];
				// 是否使用了加息劵
				$usejxj = D('Coupon')->get_jiaxijuan_by_ordernum($userid, $jyb['ordernum']);
				// 还没生成账单
				if(empty($jyb['returndate'])){
					$jyb['currperiod'] = 4;//用户当前最大期数
				}
				// 获取复利详情
				$jyblist = D('Orders')->flb_earnings_detail(1, 4, $jyb, $productCoins, $usejxj);
				//p($jyblist);
				$jyb['totallx'] = 0;
				foreach ($jyblist as $jybv){
					$jyb['totallx'] += $jybv['rate'];
				}
				//本期还款账单是否已经写入
				$billstableList = D('Bills')->is_haved_billstable($sxborderinfo['coid1'],$jyb['userid']);
				//p($billstableList);
				if($billstableList['returndate'] && $curentTime<$billstableList['returndate']){
					// 处理省心宝中的季盈宝，转投不超过4期
					if($jyb['exp_order_type']==2){
						$nowtime = time();
						$dm = ceil($this->date_month_diff($jyb['interestdate'], $nowtime)/3);
						if($dm < 4){
							$jyb['cantouzi'] = 1; // 可以进行复投
						}else{
							//$jyb['cantouzi'] = 0; // 不能进行复投
						}
					}
				}
				
				//p($jyb);
			}/** endif coid1*/
			if($sxborderinfo['coid2']){ // 子认购订单ID2-定投
				$zdy = $sxb_modle->getOrderBillById($sxborderinfo['coid2']);//专定盈
			}/** endif coid2*/
		}/** endif */
		$data['sxborderinfo'] = $sxborderinfo; // 省心宝明细
		$data['jyb'] = $jyb; // 季赢宝
		$data['jyblist'] = $jyblist; // 复利详情(复利期数)
		$data['zdy'] = $zdy; // 专定盈
		
		//echo json_encode($data);exit; // test
		return $this->_return($data);
	}/*}}}*/
	
	/**
	 * 计算月份差
	 * @param mixed $begin 开始时间
	 * @param mixed $end 结束时间
	 * @access private
	 * @return int
	 */
	private function date_month_diff($begin, $end)/*{{{*/
	{
		if(!$begin || !$end) return FALSE;
		$begin = intval($begin);
		$end= intval($end);
		//计算月份差
		$mon = date('m', $end) - date('m', $begin);
		//计算年份差
		$y = date('Y', $end) - date('Y', $begin);
		//如果年份不同
		if( $y>0 ){
			//累加月份
			$mon += $y*12;
		}
		return $mon;
	}/*}}}*/
	
	
	/**
	 * 薪资宝订单详情 
	 * http://api.88.com.cn/licaiPlan/detail?type=1&typeid=47
	 *
	 * @param mixed $typeid 计划数
	 * @param mixed $userid    用户id
	 * @access private
	 * @return void
	 */
	private function _detail_xzb($typeid, $userid)/*{{{*/
	{
		$data = array();
		$number = empty($typeid) ? 1 : $typeid; // 默认为第一个计划
		$data['number'] = $number; // 计划数
		//echo $number;
		// 获取用户计划数
		$xzb_model = D('ProductXzb');
		$periodNumber = $xzb_model->getPeriodNumber($userid);
		//p($periodNumber);
		// 拆分计划数
		$periodNumber2 = array_chunk($periodNumber, 4); // 每4个为一组
		//p($periodNumber2);
		$checknum = 0;
		foreach ($periodNumber2 as $key => $v){
			foreach ($v as $item){
				if($number == $item){
					$checknum = $key;
					break;
				}
			}
		}
		// 数据排版
		$periodNumberNew = array();
		foreach($periodNumber2 as $k=>$pn2){
			if($k >= $checknum){
				$periodNumberNew = array_merge($periodNumberNew ,$pn2);
			}
		}
		foreach ($periodNumber2 as $k=>$pn2){
			if($k < $checknum){
				$periodNumberNew = array_merge($periodNumberNew ,$pn2);
			}
		}
		//p($periodNumberNew);
		$data['periodNumber'] = $periodNumberNew; // 重新排版的计划数
		// 当前计划投资单
		$periodOrder = D('OrdersView')->getPeriodOrder($userid,$number);
		//p($periodOrder);
		if(!empty($periodOrder)){
			//获取计划明细
			$periodJihua = $xzb_model->jisuanPeriodJihua($periodOrder);
			//p($periodJihua);
			/** TODO*/
			$data['billstable'] = $periodJihua['billstable']; // 还款账单
			$data['alltotalLx'] = $periodJihua['alltotalLx']; // 总利息
			$data['allPeriod'] = $periodJihua['allPeriod']; // 计划全部期数
			$data['noYetNumber'] = $periodJihua['noYetNumber']; // 未开始期数
			$data['nextInvestDate'] = $periodJihua['nextInvestDate']; // 下次投资时间
			$data['lastInvestNumber'] = $periodJihua['lastInvestNumber'];// 上期投资期号
			$data['lastInvestMoney'] = $periodJihua['lastInvestMoney']; // 上期投资金额
			$data['lastInvestLx'] = $periodJihua['lastInvestLx']; // 上期利息
			$data['maxInvestMoney'] = $periodJihua['maxInvestMoney']; // 第一笔投资金额
			$data['nowInvest'] = $periodJihua['nowInvest']; // 投资中的期数
			$data['alltotalBj'] = $periodJihua['alltotalBj']; // 总本金
		}
		// ?? 定时投资任务 ??
		$invPlanWhere['uid'] = $userid;
		$invPlanWhere['exp_order_type'] = 1; 
		$invPlan = $xzb_model->getLicaiInvestmentPlan($invPlanWhere);
		//p($invPlan);
		$data['planNumber'] = count($periodNumber); // 总投资计划数
		$data['invPlan'] = $invPlan; // 薪资宝定时任务
		
		//echo json_encode($data);exit; // test
		return $this->_return($data);
	}/*}}}*/
	
	/**
	 *
	 * 薪资宝 开启/关闭 自动转存
	 *
	 * METHOD:POST
	 *
	 * status=1&invmoney=5000.00&eot=1&pn=44&runday=12
	 * param:
	 * status=0          0关闭，1启动
	 * invmoney=1000     计划购买金额
	 * eot=1             类型，1薪资宝
	 * pn=45             用户第几个计划
	 * runday=12         运行日期，天数
	 * 
	 * @access public
	 * @return void
	 */
	public function invplan()/*{{{*/
	{
		//$userid = $map['userid'] = 204865; // 测试
		$userid = self::$current_uid;
		
		$xzb_modle = D('ProductXzb');
		$exp_order_type = I('post.eot') + 0; // 固定1：薪资宝
		$period_number = I('post.pn') + 0; // 用户第几个计划数
		$invmoney = I('post.invmoney'); // 开启计划金额
		$status = I('post.status'); // 状态
		$runday = I('post.runday');
		if (empty($exp_order_type) && empty($period_number)
			&& empty($invmoney) && empty($runday)) {
			/** TODO */
			//exit('参数错误');
			return $this->_return(self::_error(440, '参数错误'));
		}
		// 获取用户某个定时投资计划
		$oneLP = $xzb_model->getOneLicaiInvestmentPlan($userid, $period_number);
		// 组装数据
		$data['exp_order_type'] = $exp_order_type;
		$data['period_number'] = $period_number;
		$data['invmoney'] = $invmoney;
		$data['status'] = $status;
		$data['runday'] = $runday;
		if ($oneLP && $oneLP['id']) { // 数据更新
			$data['updatetime'] = time();
			$result = $xzb_model->editLicaiInvestmentPlan($oneLP['id'], $data);
		} else { // 进行添加
			$data['createtime'] = time();
			$result = $xzb_model->addLicaiInvestmentPlan($data);
		}
		if ($result) {
			/** TODO */
			//exit('设定成功');
			return $this->_return(self::_error(self::E_OK, '设定成功'));
		} else {
			/** TODO */
			//exit('设定失败');
			return $this->_return(self::_error(441, '设定失败'));
		}
	}/*}}}*/
	
}

/* end file */
