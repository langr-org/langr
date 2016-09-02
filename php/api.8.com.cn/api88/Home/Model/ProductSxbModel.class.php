<?php
namespace Home\Model;
use Think\Model;

/**
 * 省心宝数据模型
 * 
 * @uses Model
 * @package 
 * @version $id$
 * @copyright 1997-2005 The PHP Group
 * @author wenming.pan <wenming.pan@outlook.com> 
 * @license PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class ProductSxbModel extends Model {
    protected $trueTableName = 'data_orders_plan';
    protected $dbName = 'order_data';

    /**
     * getOrdersRows
     * 获取省心宝订单个数 
     * 
     * @param mixed $userid 用户id
     * @param mixed $type 1:进取型 2：稳健性
     * @access public
     * @return int
     */
    public function getOrdersRows($userid,$type){
        $where['userid'] = $userid;
        $where['otype'] = $type;
        $where['payStatus'] = 2;        
        return $this->where($where)->count();
    }

    /**
     * 获取用户所有的省心宝 
     * 
     * @param mixed $userid 
     * @access public
     * @return void
     */
    public function getSxbOrders($userid){
        $where['userid'] = $userid;
        $where['payStatus'] = 2;
        return $this->where($where)->order('id asc')->select();
    }

    /**
     * 获取订单和账单 
     * 
     * @param mixed $id 子订单id 
     * @access public
     * @return void
     */
    public function getOrderBillById($id){
        $where['orders.id'] = $id;
        return D('OrdersBillView')->where($where)->find();
    }
}
