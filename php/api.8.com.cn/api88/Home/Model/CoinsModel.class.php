<?php
namespace Home\Model;
use Think\Model;

/**
 * 88 币 
 * 
 * @uses Model
 * @package 
 * @version $id$
 * @copyright 1997-2005 The PHP Group
 * @author wenming.pan <wenming.pan@outlook.com> 
 * @license PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class CoinsModel extends Model {
    protected $trueTableName = 'data_coinslog';
    protected $dbName = 'log_data';
    
    /**
     * 获取产品使用的88币  
     * 
     * @param int $userid 用户id
     * @param int $orderid 订单id
     * @access public
     * @return array
     */
    public function getProductCoins($userid, $orderid){
        $where['uid'] = $userid;
        $where['orderNum'] = $orderid;
        $where['channel'] = 2;
        $result = $this->field('tradeNum')->where($where)->find();
        return $result;  
    }

}
