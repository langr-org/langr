<?php
namespace Home\Model;
use Think\Model;

/**
 * 代金券模型 
 * 
 * @uses Model
 * @package 
 * @version $id$
 * @copyright 1997-2005 The PHP Group
 * @author wenming.pan <wenming.pan@outlook.com> 
 * @license PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class CouponModel extends Model {
    protected $trueTableName = 'data_coupon';
    protected $dbName = 'products_data';

    /**
     * 通过订单编号获取代金券  
     * 
     * @param int $userid 用户id
     * @param string $ordernum  订单号
     * @access public
     * @return array
     */
    public function get_coupon_by_ordernum($userid,$ordernum){
        $where['data_coupon_detail.userid'] = $userid;
        $where['data_coupon_detail.orderNumber'] = $ordernum;
        $join = "LEFT JOIN {$this->dbName}.data_coupon_detail on {$this->dbName}.data_coupon_detail.couponid=data_coupon.id";
        $field = "data_coupon_detail.*, data_coupon.name, data_coupon.offline_type, 
            data_coupon.online, data_coupon.type, data_coupon.price, data_coupon.minOrderPrice, 
            data_coupon.investTime, data_coupon.provideStartTime, data_coupon.provideEndTime, 
            data_coupon.useStartTime, data_coupon.useEndTime";
        return $this->join($join)->field($field)->where($where)->find();
        p($result);
    }

    
    /**
     * 通过订单编号获取加息券 
     * 
     * @param int $userid 用户id 
     * @param string $ordernum  订单号
     * @access public
     * @return array
     */
    public function get_jiaxijuan_by_ordernum($userid,$ordernum){
        $where['data_jiaxijuan_detail.userid'] = $userid;
        $where['data_jiaxijuan_detail.orderNumber'] = $ordernum;
        $field = "data_jiaxijuan_detail.id, data_jiaxijuan_detail.orderNumber,
            data_jiaxijuan_detail.userid, data_jiaxijuan_detail.jxjid,
            data_jiaxijuan.name, data_jiaxijuan.interest";
        $result = $this->table("{$this->dbName}.data_jiaxijuan_detail")
            ->join("LEFT JOIN {$this->dbName}.data_jiaxijuan on {$this->dbName}.data_jiaxijuan_detail.jxjid = {$this->dbName}.data_jiaxijuan.id")
            ->field($field)
            ->where($where)
            ->find();
        return $result;
    }

	/**
	 * 检测产品是否能使用加息劵 
	 * 
	 * @param int $pid 产品id 
	 * @access public
	 * @return array 
	 */
	public function check_jiaxijuan_product($pid)/*{{{*/
	{
		$where['productid'] = $pid;	
		$result = $this->table("{$this->dbName}.data_jiaxijuan_pro")
			->where($where)
			->select();
		$ids = array();
		if (count($result)) {
			foreach ($result as $key=>$val) {
				$ids[] = $val['jxjid'];
			}
			return $ids;
		}
		return $ids;
	}/*}}}*/


}
