<?php

namespace Home\Model;
use Think\Model\ViewModel;

/**
 * 我的投资订单投资视图模型
 */
class OrdersViewModel extends ViewModel{
	public $viewFields = array(
		'order_data.orders'=>array('id', 'pdid', 'userid', 'ordernum', 'money',
                    'accountMoney' => 'accountmoney', 'investType' => 'investtype', 
                    'investTime' => 'investtime', 'interestDate' => 'interestdate', 'yly', 
                    'totalLx' => 'totallx', 'payType' => 'paytype', 'payStatus'=>'paystatus', 
                    'order_type', 'redeem', 'inDate' => 'indate','exp_order_type',
                    'period_number','zrtime','period_first_orderid','period_item',
                    'currprincipal','compoundTime','currperiod','pduserid',
		            '_as'=>'orders','_type'=>'LEFT'
                    ),
        'products_data.products'=>array('id'=>'pid','productName'=>'productname', 
                    'pType' => 'ptype', 'exp_pType' => 'exp_ptype', 'isgyl', 'status',
                    'raiseBody' => 'raisebody', 'isxd', 'rgEndtime' => 'rgendtime',
			        'istransfer', 'isnodb', 'transfer_difftime',
			        '_as'=>'products','_on'=>'orders.pdid=products.id'
			),
	);

    /**
     * 薪资宝：获取用户当前期数的投资订单  
     * 
     * @param int $uid 用户id
     * @param int $period_number 投资计划数
     * @param int $eot (exp_order_type)1: 薪资宝订单, 2: 省心宝, 3: 体验标
     * @access public
     * @return void
     */
    public function getPeriodOrder($uid, $period_number, $eot = 1){
        $where['userid'] = $uid;
        $where['paystatus'] = array('in', array(0,2));
        $where['exp_order_type'] = $eot;
        $where['period_number'] = $period_number;
        
        $result = $this->where($where)->order('id asc')->select();
        return $result;
    }

    /**
     * 获取用户订单信息  
     * 
     * @param mixed $userid 用户id
     * @param mixed $orderid 订单id
     * @access public
     * @return void
     */
    public function get_order_byid($userid,$orderid){
        $where['userid'] = $userid;
        $where['id'] = $orderid;
        
        $result = $this->where($where)->find();
        return $result;

    }
}

