<?php
/**
 * @file WithdrawController.class.php
 * @brief 确认产品 获取用户可用福利卡券
 * 
 * Copyright (C) 2016 ZKC.com
 * All rights reserved.
 * 
 * @package Controller
 * 
 * by nongchunxiu 2016/08/29
 */

namespace Home\Controller;

use Think\Controller; 
use Home\Controller\AppController;
use Home\Model\ProductsModel;
use Home\Model\CouponModel;
use Home\Model\UsersModel;
use Home\Model\WithDrawModel;

class WelfarecardController extends AppController
{
	public function index() /* {{{ */
	{
		$token = self::$current_token;
		$uid = self::$current_uid;
		$appid = self::$current_appid;
		$usermodel = new UsersModel();
		$userinfo = $usermodel->getinfo($uid);
		switch ($_SERVER['REQUEST_METHOD']) {
		case 'GET' :
			$this->_get_welfcards($userinfo);
			break;
		case 'POST' :
			$this->_check_idcard($userinfo);
			break;
		case 'PUT' :
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

	/***获取用户当前产品可使用的福利卡券*****/
	private function _get_welfcards($userinfo){
		$coupmodel = new CouponModel();
		$promodel = new ProductsModel();
		$money = I('get.money') +0;
		$pdid =  I('get.pdid') +0;
		if( $money<=0 || $pdid<=0 ){
			return $this->_return(self::_error(self::E_ARGS));
		}
		/***校验产品***/
		$proinfo = $promodel->product_detail( $pdid );
		if( empty( $proinfo ) || $proinfo['status'] !=2 ){
			return $this->_return(self::_error(480,'产品不存在或不在认购期！'));
		}
		/***当前订单可使用de代金券***/
		if( $proinfo['isdjj']==1 ){
			$cous = $coupmodel->get_coucards_byuid( $uid , $money );
		}else{
			$cous = array();
		}
		/***当前订单可使用de收益增值券***/
		$jxq = $coupmodel->get_jxqcards_byuid( $uid , $pdid , $money );
		return $this->_return( array( "coupons"=>$cous , "jxq"=>$jxq ) );
	}

	/******用户实名认证********/
	private function _check_idcard($userinfo){
		$ac = I('post.ac');
		$uid = $userinfo['userid'] +0;
		$realname = I('post.realname');
		$idcard = I('post.idcardno');
		if( $realname=="" || $idcard==""){
			return $this->_return(self::_error(self::E_ARGS));
		} 
		/**已经实名认证***/
		if( $userinfo['realname'] !="" OR $userinfo['IDcardNo'] !="" ){
			return $this->_return(self::_error(467,'已经实名验证！'));
		}
		$usermodel = new UsersModel();
		$ishaveuser = $usermodel->check_idcard( $realname , $idcard ); 
		if( !empty($ishaveuser) && $ishaveuser['userid'] !=$uid ){
			return $this->_return(self::_error(self::E_OP_FAIL)); //423 身份证号已被使用
		}

		/***认证次数***/
		$redis = $this->data_store->FRedis;
		$key = 'checkID:'.$uid; 
		$check_result = $redis->hGetAll($key);
		// //添加记录
		$chs = $check_result[$key]+1;
		$v = array($key=>$chs);
		$redis->hMset($key, $v);
		/* 认证24小时后过期 */
		$redis->setTimeout($key, 86400);
		if(  $check_result[$key]<3 ){
    		/**更新用户信息**/
    		$res = $usermodel->update_realname( $realname,$idcard,$uid );
    		/**更新用户统计表中的用户信息**/
    		if( $res ){
    			$reusermodel = new ReportsUsersModel();
    			$res1 = $reusermodel->updata_report_idcheck($uid);
    		}
    		$newinfo = $usermodel->getinfo($uid);
    		return $this->_return(self::_error(self::E_OK,'OK'));
    		/****
    		*
    		* ****************此处缺失旧的API 广告系统代码*******************
    		*
    		****/
		}else{
			/**认证错误已到三次，请24小时后再认证！**/
			return $this->_return(self::_error(self::E_OP_FAIL)); 
		}


	}

}

/* end file */
