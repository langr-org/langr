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
use Home\Model\OrdersModel;
class PlaceorderController extends AppController
{
	public function index() /* {{{ */
	{
		$token = self::$current_token;
		$uid = self::$current_uid;
		$appid = self::$current_appid;

		/** 获取用户信息 */
		$usermodel = new UsersModel();
		$userinfo = $usermodel->getinfo($uid);
		switch ($_SERVER['REQUEST_METHOD']) {
		case 'GET' :
			// $this->_get_welfcards($userinfo);
			break;
		case 'POST' :
			$this->_submit_order( $userinfo );
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


	private function _submit_order($userinfo){
		$uid = $userinfo['userid'];
		$coupmodel = new CouponModel();
		$promodel = new ProductsModel();
		$ordermodel = new OrdersModel();
		$money = I('post.money') +0;
		$pdid = I('post.pdid') +0;
		$yll = I('post.yll') +0;//产品年利率        
        $investTime = I('post.investTime') +0; //投资时间
        $time = time();
        /***只能使用一种福利卡券***/
        $conpou_detail_id = I('post.conpou_detail_id');
        $conpou_detail_type = I('post.conpou_detail_type'); //福利券类型 
        $jiaxijuan_detail_id = 0;
        if ($conpou_detail_type==2){
				$jiaxijuan_detail_id =  $conpou_detail_id;
				$conpou_detail_id=0;
		}
		if( $userinfo['realname']=="" || $userinfo['idcardno']=="" ){
			$this->_return(self::_error(441,'未实名认证！'));
		}
		$proinfo = $promodel->product_detail( $pdid );
		
		/****校验参数信息 产品是否存在 参数与产品信息是否相符***/
		if( $yll != $proinfo['hoperate1'] || $investTime !=$proinfo['investtime'] ||  empty($proinfo) || !is_numeric( $money ) || $money<=0 || $pdid<=0 ){ 
			$this->_return(self::_error(self::E_DATA_INVALID)); //年利率、期限与产品信息不符
		}
		
		/**产品是否结束认购**/ 
        if (($proinfo['rgEndtime'] && $proinfo['rgEndtime'] <= $time) || $proinfo['status'] !=2 ) {
            $this->_return(self::_error(410,'该产品已经认购结束，移步看看其他产品吧！'));
        }
        if( $proinfo['is_new']==1 ){
        	$count_uo = $ordermodel->count_user_orders($uid);
        	if( $count_uo >0 ){
        		$this->_return(self::_error(411,'认购失败，不是新注册用户，不能购买新手产品！'));
        	}
        }
        if( $proinfo['beginmoney']>0 &&  $money<$proinfo['beginmoney'] ){
        	$this->_return(self::_error(412,'认购金额不能小于起投金额'.$proinfo['beginmoney']));
        }
        if( $proinfo['maxmoney'] >0 && $money>$proinfo['maxmoney'] ){
        	$this->_return(self::_error(412,'认购金额不能大于最大可投金额'.$proinfo['maxmoney']));
        }
        if( $money> ( $proinfo['totalamount']-$proinfo['totalmoney'] ) ){
        	$this->_return(self::_error(412,'超过剩余可投金额'.( $proinfo['totalamount']-$proinfo['totalmoney'] ).'元！'));
        }
        $alreadybuy = $ordermodel->count_user_orders_money($uid,array('pdid'=>$pdid));
        /**已经买过的+下单的金额 不能大于产品设置的最大可投金额**/
        if( $proinfo['maxmoney']>0 && ( $alreadybuy['accountmoney'] + $money ) >$proinfo['maxmoney'] ){
        	$this->_return(self::_error(412,'认购金额不能大于最大可投金额'.$proinfo['maxmoney']));
        }
        if( $conpou_detail_id !=0 && $proinfo['isdjj']==0 ){
        	$this->_return(self::_error(413,'认购的产品不能使用代金券！'));
        }
        if( $conpou_detail_id >0 ){
        	$where['d.userid']=$uid; 
        	$coudetail = $coupmodel->get_coupon_detail( $conpou_detail_id , $where );
        	if( empty( $coudetail ) ){
        		$this->_return(self::_error(414,'代金券不存在！'));
        	}elseif ( $coudetail['status']==1 || $coudetail['ordernum'] !="" ) {
                $this->_return(self::_error(415,'代金券已使用！'));
            }elseif( $coudetail['userEnd']< $time ){
        		$this->_return(self::_error(416,'代金券已过期！'));
        	}elseif( $coudetail['userStart']> $time ){
        		$this->_return(self::_error(417,'代金券不在使用期限内！'));
        	}elseif ( $coudetail['minOrderPrice'] >$money ) {
        		$this->_return(self::_error(418,'最少投资'.$coudetail['minOrderPrice'].'才能使用该代金券！'));
        	}
       	}
       	if( $jiaxijuan_detail_id >0 ){
       		//产品是否能使用加息券
       		$where['d.userid']=$uid; 
       		$jxqdetail = $coupmodel->get_jxq_detail( $jiaxijuan_detail_id , $where );

       		if( empty( $jxqdetail ) ){
       			$this->_return(self::_error(414,'收益增值券不存在！'));
       		}elseif ( $jxqdetail['status']==1 ) {
       			$this->_return(self::_error(415,'收益增值券已使用！'));
       		}elseif( $jxqdetail['userEnd']< $time ){
        		$this->_return(self::_error(416,'收益增值券已过期！'));
        	}elseif( $jxqdetail['userStart']> $time ){
        		$this->_return(self::_error(417,'收益增值券不在使用期限内！'));
        	}elseif ( $jxqdetail['minOrderPrice'] >$money ) {
        		$this->_return(self::_error(418,'最少投资'.$coudetail['minOrderPrice'].'才能使用该收益增值券！'));
        	}
        	if( !empty( $jxqdetail ) ){
        		$jxqpro = $coupmodel->get_jxq_pro(array( 'productid'=>$pdid,'jxjid'=>$jxqdetail['jxjid']));
        		if( empty($jxqpro) ){
        			$this->_return(self::_error(413,'该产品不在该收益增值券使用范围！'));
        		}
        	}
       	}

       	/***********校验ending********************/
       	/**************订单数据*******************/
       	if( $conpou_detail_id >0 ){
       		$money += $coudetail['price']; //把代金券增加到认购本金中
       	}
       	$yll = $proinfo['hoperate1']/100;
       	if( $jiaxijuan_detail_id >0 ){
       		$yll = ($proinfo['hoperate1']+$jxqpro['interest']) /100; //加上加息券利率
       	}
       	$qxtime = strtotime(date('Y-m-d', $time)) + 86400; //起息时间为认购的第二天
       	$interestDate = $proinfo['jxtime']; // 产品设置计息时期
        if ($interestDate == 0 || $interestDate == '') {
            $interestDate = $qxtime;
        }
        /*****订单类型*****/
        $order_type = $promodel->get_order_type($proinfo['pType']);
        if ($isnodb == 0) {
            $order_type = $promodel->get_order_type($proinfo['pType']); 
        } else {
            $order_type = 4;
        }
        /*****计算认购天数******/
        $orderTimeDays = $investTime;
        if ($proinfo['investtype'] == 'month') {
            //计算认购天数
            $orderTimeDays = (strtotime('+' . $investTime . ' month', $interestDate) - $interestDate) / (24 * 60 * 60);
        }
        //总利息
        $totalLx = $money * $yll / 365 * $orderTimeDays;
        $usermodel = new UsersModel();
        $uinfo = $usermodel->get_user_info( $uid );
        // 保存认购单 // 如果保存成功，返回订单ID
        $res = $ordermodel->add_order($pdid, $proinfo['userid'], $uinfo, ($yll * 100), $money, 0, '', $proinfo['investtype'], $investTime, $totalLx, $order_type); 
        $array = explode(',', $res);
        $orderid= trim($array[0]);
        $ordernum = $array [1];

        if ($orderid != '') {
        	//修改产品已募资总额
        	$updatetotalMoney = $promodel->update_product_totalmoney($pdid, $money);
            // 验证订单并获取订单信息
            $order = $ordermodel->get_orders_detail( array('userid'=>$uid,'ordernum'=>$ordernum) );
            if ( $order['id']==0 ) {
                $this->_return(self::_error(442,'订单更新失败！'));
            }               
            if ( $conpou_detail_id > 0) {//改变代金券状态
                $mcoupon->change_coupon_status($conpou_detail_id, $ordernum);              
            }
        	// 改变收益增值券状态
			if ($jiaxijuan_detail_id>0) { 
					$mcoupon->change_jxqdetail_status($jiaxijuan_detail_id, $ordernum);
			} 
            $this->_return( array("ordernum"=>$ordernum) );
        }

	}



}

/* end file */
