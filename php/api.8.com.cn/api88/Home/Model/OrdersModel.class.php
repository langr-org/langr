<?php
namespace Home\Model;
use Think\Model;

/**
 * 我的投资 订单模型
 * 
 * @uses Model
 * @package 
 * @version $id$
 * @copyright 1997-2005 The PHP Group
 * @author wenming.pan <wenming.pan@outlook.com> 
 * @license PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class OrdersModel extends Model {
    protected $trueTableName = 'data_orders';
    protected $dbName = 'order_data';
    
    /**
     * 获取投资中的订单
     * 
     * @access public
     * @return void
     */
    public function get_invest_orders(){
        
    }

    public function test(){
        p($this->table('billstable_data.billstable'));
    }

    /**
     * 重阳感恩季 暖心好礼来孝亲——投资即送高达90万保额的孝心保单  
     * 
     * @param int $ordernum 订单id
     * @param int $userid 用户id
     * @access public
     * @return array
     */
    public function get_cyj_order($ordernum,$userid){
        $where['type'] = 1;
        $where['userid'] = $userid;
        $where['ordernum'] = $ordernum;
        $result = $this->table('log_data.data_cyj')->where($where)->find();
        //echo $this->getLastSql(),'<br/>';
        return $result;
    }

    /**
     * 获取省心宝 投资计划  
     * 
     * @param int $coid2 订单id(子认购订单-定投)
     * @access public
     * @return array
     */
    public function get_ordersplan_info($coid2){
        $where['data_orders_plan.coid2'] = $coid2;
        $field = "data_orders_plan.*, data_products_plan.yield";
        $result = $this->table("{$this->dbName}.data_orders_plan")
            ->join("LEFT JOIN products_data.data_products_plan on {$this->dbName}.data_orders_plan.pdid=products_data.data_products_plan.id")
            ->field($field)
            ->where($where)
            ->order('data_orders_plan.id asc')
            ->find();
        return $result;
        p($result);
    }

    /**
     * flb_earnings_detail
     * 复利宝投资列表详情 
     * 
     * @param mixed $page  
     * @param mixed $pagesize 
     * @param mixed $order    订单
     * @param int $coins      88币
     * @param mixed $usejxj   加息劵
     * @access public
     * @return void
     */
    public function flb_earnings_detail($page,$pagesize,$order,$coins=0, $usejxj=''){
        $returnArr = array();
        $max = $order['currperiod']; // 最大期数
        $offset=($page-1)*$pagesize;//0
        $end = $offset + $pagesize;//10
        if($end > max){
            $end = $max;
        }
        // 是否使用加息劵
        $isusejxj = $usejxj;
        $investtype = $order['investtype']; // 计息类型(月or天)
        $investtime = $order['investtime']; // 时间
        $yly = $order['yly']; // 年利率
        $dly = $yly / 365; // 日利率
        $money = $bj = $order['accountmoney']; // 记录上一次本金
        $prevdate = $interestdate = $order['interestdate']; // 计息日
        $i = 0;
        while ($i < $end){
            $item = array();
            $item['index'] = $i + 1;
            if($investtype == 'month'){
                // 本期结束时间
                $item['nextdate'] = strtotime("+".($investtime*$item['index'])." month", $interestdate);
            }elseif($investtype == 'day'){
                $item['nextdate'] = strtotime("+".$investtime." day", $prevdate);
            }
            // 本期开始时间
            $item['date'] = ($i==0) ? $interestdate : $prevdate;
            // 上一期的结束时间
            $prevdate = $item['nextdate'];
            // 本期间隔天数
            $spaceday = ($item['nextdate']-$item['date'])/86400;
            //当前期的本金
            $item['money'] = ($i==0) ? $bj : $money;
            // 使用加息劵情况
            if(isset($isusejxj) && !empty( $isusejxj['id']) && $i >0){
                $yly = $order['yly'] - $isusejxj['interest'];
                $dly = $yly / 365;
            }
            //本期产生的利率
            if($i==0){ //针对88币
                $item['rate'] = ($money+$coins)*$dly*$spaceday/100;
            }else{
                $item['rate'] = $money*$dly*$spaceday/100;
            }
            //下一期的本金
            $money = $item['money']+$item['rate'];
            $item['money'] = number_format($item['money'], 2, ".","");
            $item['rate'] = number_format($item['rate'], 2, ".","");
            $item['yly'] = $yly;
            $item['redeem'] = $order['redeem']; // 0:未赎回;1:已赎回;2:转让中;3:转让成功
            /*最后一期时间纠正*/
            if($i+1==$end && $i!=0){
                if($investtype == "month"){
                    // 本期结束时间
                    $item['date'] = strtotime("+".$investtime." month", $returnArr[$i-1]['date']); 
                }
            }
            array_push($returnArr, $item);
            $i++;
        }/*end while*/

        return array_slice($returnArr, $offset, $pagesize);
    }
}
