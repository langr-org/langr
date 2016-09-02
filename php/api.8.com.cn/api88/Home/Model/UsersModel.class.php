<?php
/**
 * @file UsersModel.class.php
 * @brief 
 * 
 * Copyright (C) 2016 ZKC.com
 * All rights reserved.
 * 
 * @package Model
 * @author Langr <hua@langr.org> 2016/05/23 15:02
 * 
 * $Id$
 */

namespace Home\Model;
use Think\Model;
class UsersModel extends Model 
{
	//protected $tableName = 'users';
	//protected $tablePrefix = 'data_';
	protected $trueTableName = 'data_users';
	protected $dbName = 'members_data';
	protected $pk = 'userid';
	//protected $fields = array('userid','username','tj_mobile');
	protected $_map = array(
		'usertype'=>'userType',
		'username'=>'userName',
		'realname'=>'realName',
		'idcardno'=>'IDcardNo',
		'password'=>'passWord',
		'paypassword'=>'payPassWord',
		'mymoney'=>'myMoney',
		'freezemoney'=>'freezeMoney',
		'myintoid'=>'myIntoID',
		'toprecommend'=>'topRecommend',
		'mygroupid'=>'myGroupID',
		'mymanageid'=>'myManageID',
		'loginnum'=>'loginNum',
		'prelogindate'=>'preloginDate',
		'lastlogindate'=>'lastloginDate',
		'islock'=>'isLock',
		'moneylock'=>'moneyLock',
		'regtime'=>'regTime',
		'apploginnum'=>'apploginNum',
	);
	
	/**
	 * @brief 会员登录
	 * @param name user_name,tel,email
	 * @param pwd md5(password)
	 * @return userinfo/false
	 */
	public function login($name, $pwd) /* {{{ */
	{
		$res = $this->where("username='$name' or tj_mobile='$name' or email='$name'")->select();
		//$res = $this->parseFieldsMap($res);
		if ( $res && md5($pwd) == $res[0]['password'] ) {
			/* 记录登录时间和登录次数 */
			$this->where('userid='.$res[0]['userid'])->save(array(
					'prelogindate'=>$res[0]['lastlogindate'],
					'lastlogindate'=>time(),
					'loginnum'=>array('exp', 'loginnum+1'),
					'terminal'=>0));
			return $res[0];
		}
		return false;
	} /* }}} */

	/**
	 * @brief 会员注册
	 * @param $d = array(username,md5(password),mobile,email...)
	 * 	appid: 0 pc,1 wap,h5, 2 android, 3 iphone,iosx
	 * @return userinfo/false
	 */
	public function register($d) /* {{{ */
	{
		if ( $d['appid'] == 'pc' ) {
			$d['appid'] = 0;
		} else if ( $d['appid'] == 'h5' || $d['appid'] == 'wap' ) {
			$d['appid'] = 1;
		} else if ( $d['appid'] == 'android' ) {
			$d['appid'] = 2;
		} else {
			$d['appid'] = 3;
		}
		$d['tj_mobile'] = $d['mobile'];
		$d['dfmobile'] = _dfzip($d['mobile']);
		$d['password'] = md5($d['password']);
		$d['regtime'] = time();
		$d['regip'] = CLIENT_IP;
		$intro = $this->get_recommend($d['recommend']);
		$d += $intro;

		$this->create($d);
		$userid = $this->add();
		if ( !$userid ) {
			return false;
		}

		/* DO Other */
		$this->register_after($userid);
		return $userid;
	} /* }}} */

	/**
	 * @brief 注册完成之后相关事件处理
	 * @access protected
	 * @param $userid 注册账号userid
	 * @return null
	 */
	protected function register_after($userid) /* {{{ */
	{
		return ;
	} /* }}} */

	/**
	 * @brief 推荐人信息查找
	 * @param $recommend number/mobile
	 * @return recommend userid/false
	 */
	public function get_recommend($recommend = null) /* {{{ */
	{
		$myintoid = 0;
		$userform = 0;
		$toprecommbend = 0;
		$mymanageid = 0;
		if ( empty($recommend) ) {
			//$inv = cookie('inv');
			$inv = $_COOKIE['inv'];
			if ( empty($inv) ) {
				$myintoid = 0;
				$userform = 0;
			} else if ( strlen($inv) >= 7 ) {
				$myintoid = intval(substr($inv, 6));
			     	$userform = 1;
			     	$importtype = 2;
			     	$isimport = 1;
				$toprecommend = 0;
			} else if ( strlen($inv) >= 3 ) {
				$myintoid = intval($inv/2);
			     	$userform = 1;
			     	$importtype = 2;
			     	$isimport = 1;
				$toprecommbend = 0;
			} else if ( $inv == 'bd' ) {	/* baidu */
				$myintoid = 0;
			     	$userform = 2;
			} else if ( $inv == 'wm' ) {	/* 百度网盟 */
				$myintoid = 0;
				$userform = 4;
			}
		}
		$res = array();
		if ( strlen($recommend) > 10 ) {
			$res = $this->where("username='$recommend' or tj_mobile='$recommend' or dfmobile='$recommend'")->field('userid,topRecommend toprecommend')->find();
		} else if ( !empty($recommend) ) {
			$res['userid'] = intval(intval($recommend)/2);
		}
		if ( !empty($res) ) {
			$userform = 1;
			$ismobile = 1;
			$myintoid = $res['userid'];
			/* TODO: */
			$toprecommbend = 0;
		}
		return array('myIntoID'=>$myintoid,'topRecommend'=>$toprecommbend,'myManageID'=>$mymanageid,'userform'=>$userform);
	} /* }}} */

	/**
	 * @brief 会员信息
	 * @param $uid
	 * @return userinfo
	 */
	public function getinfo($uid) /* {{{ */
	{
		//import('Vendor.zkc.FRedis', APP_PATH, '.php');
		$redis = \FRedis::getCacheSingleton();
		$key = KEY_PREFIX.':userinfo:'.$uid;
		$info = $redis->hGetAll($key);
		if ($info) {
			return $info;
		}

		$res = $this->where("userid='$uid'")->select();
		if ($res) {
		}

		return false;
	} /* }}} */

	/**
	 * @brief 检测手机是否使用
	 * @param $mobile
	 * @return true 正常可使用，false 已经使用
	 */
	public function check_mobile($mobile) /* {{{ */
	{
		$res = $this->where("dfmobile='"._dfzip($mobile)."' or tj_mobile='$mobile'")->select();
		if ($res) {
			return false;
		}

		return true;
	} /* }}} */

	/**
	 * @brief 检测用户名是否使用
	 * @param $name
	 * @return true 正常可使用，false 已经使用
	 */
	public function check_username($name) /* {{{ */
	{
		$res = $this->where("username='$name'")->select();
		if ($res) {
			return false;
		}

		return true;
	} /* }}} */
}

/* end file */
