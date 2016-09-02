<?php
namespace Home\Model;

use Think\Model;

/**
 * 订单表(orders)所对应的账单表模型
 * 
 * @uses Model
 * @package 
 * @version $id$
 * @copyright 1997-2005 The PHP Group
 * @author wenming.pan <wenming.pan@outlook.com> 
 * @license PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class BillsModel extends Model {
    protected $trueTableName = 'data_billstable';
    protected $dbName = 'billstable_data';

    /**
     * 返回含有未还款的订单id 
     * 
     * @param int $uid 用户id 
     * @access public
     * @return string
     */
    public function get_norepayment_ids($uid){
        $where['userid'] = $uid;
        $where['isReturn'] = 0;
        $res = $this->field('orderid')->where($where)->group('orderid')->select();
        foreach ($res as $v){
            $result[] = $v['orderid'];
        }
        if(count($result)){
            $str = implode(',', $result);
            return $str;
        }
        return false;
    }

    /**
     * 获取某个订单的未还款账单  
     * 
     * @param int $orderid 订单id
     * @param int $uid 用户id
     * @access public
     * @return array
     */
    public function get_billstable_unreturn($orderid, $uid){
        $where['orderid'] = $orderid;
        $where['userid'] = $uid;
        $where['isReturn'] = array('in',array(0,4));
        $result = $this
            ->field('month, maxmonth, returnDate as returndate, moneyBj as moneybj')
            ->where($where)
            ->order('month asc')
            ->find();
        return $result;
    }


    /**
     * 根据订单查询还款账单  
     * 
     * @param mixed $orderid 订单id
     * @access public
     * @return void
     */
    public function getBillstable($orderid){
        return $this->where(array('orderid'=>$orderid))->find();
    }


    /**
     * 返回返款数(是否返款完毕)  
     * 
     * @param int $orderid 订单id
     * @param int $productid 产品id
     * @access public
     * @return int
     */
    public function is_return_count($orderid, $productid){
        $where = "orderid='{$orderid}' and pdid='{$productid}' and isReturn=1";
        return $this->where($where)->count();
    }

    /**
     * 根据用户ID和订单ID获取还款账单表中的总利息  
     * 
     * @param int $userid 用户id
     * @param int $orderid 订单id
     * @access public
     * @return float 
     */
    public function getRealFloatLX($userid,$orderid){
        $where['userid'] = $userid;
        $where['orderid'] = $orderid;
        return $this->where($where)->sum('moneyLx');

    }

    /**
     * 获取用户还款明细  
     * 
     * @param mixed $where  查询条件
     * @param mixed $page   当前页
     * @param mixed $pagesize 最大数
     * @access public
     * @return void
     */
    public function get_dt_return_list($where,$page,$pagesize){
        $offset=($page-1)*$pagesize;
        return $this->where($where)->order('month asc')->limit($offset, $pagesize)->select();
    }

    /**
     * 查询未付款账单  
     * 
     * @param mixed $oid 
     * @access public
     * @return void
     */
    public function sel_non_payment($oid){
        $where['orderid'] = array('in', array($oid));
        $where['isReturn'] = 0;
        //p($where);
        return $this->field('orderid')->where($where)->group('orderid')->select();
        //echo  D("Bills")->getLastSql();exit;
    }

    /**
     * 获取还款明细个数
     * 
     * @param mixed $where 
     * @access public
     * @return void
     */
    public function get_dt_return_list_count($where){
        //p($where);
        return $this->where($where)->count(); 
    }

    /**
     * 获取以还款明细个数 
     * 
     * @param mixed $where 
     * @access public
     * @return void
     */
    public function get_dt_return_list_return_count($where){
        return $this->where($where)->count();
    }

    /**
     * 是否存在该期的还款记录 
     * 
     * @param mixed $orderid 订单id
     * @param mixed $userid 用户id
     * @access public
     * @return void
     */
    public function is_haved_billstable($orderid,$userid){
        $where['orderid'] = $orderid;
        $where['userid'] = $userid;
        return $this->where($where)->find();
    }
}
