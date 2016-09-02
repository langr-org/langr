<?php

namespace Home\Model;
use Think\Model\ViewModel;

/**
 * 我的投资订单投资视图模型
 */
class OrdersBillViewModel extends ViewModel{
	public $viewFields = array(
		'order_data.orders'=>array('id', 'pdid', 'userid', 'ordernum', 'money',
                    'accountMoney' => 'accountmoney', 'investType' => 'investtype', 
                    'investTime' => 'investtime', 'interestDate' => 'interestdate', 'yly', 
                    'totalLx' => 'totallx', 'payType' => 'paytype', 'payStatus'=>'paystatus', 
                    'order_type', 'redeem', 'inDate' => 'indate','exp_order_type',
                    'period_number','zrtime','period_first_orderid','period_item',
                    'currprincipal','compoundTime','currperiod',
		            '_as'=>'orders','_type'=>'LEFT'
                    ),
        'products_data.products'=>array('id'=>'pid','productName'=>'productname', 
                    'pType' => 'ptype', 'exp_pType' => 'exp_ptype', 'isgyl', 'status',
                    'raiseBody' => 'raisebody', 'isxd', 'rgEndtime' => 'rgendtime',
			        'istransfer', 'isnodb', 'transfer_difftime',
			        '_as'=>'products', '_on'=>'orders.pdid=products.id', '_type'=>'LEFT'
			),
        'billstable_data.billstable'=>array('isReturn','returnDate',
			'_as'=>'billstable','_on'=>'orders.id=billstable.orderid'
			),
	);
}

