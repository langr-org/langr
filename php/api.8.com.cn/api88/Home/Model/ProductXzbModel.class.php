<?php
namespace Home\Model;
use Think\Model;

/**
 * 产品->薪资宝 
 * 
 * @uses Model
 * @package 
 * @version $id$
 * @copyright 1997-2005 The PHP Group
 * @author wenming.pan <wenming.pan@outlook.com> 
 * @license PHP Version 3.0 {@link http://www.php.net/license/3_0.txt}
 */
class ProductXzbModel extends Model {
    protected $trueTableName = 'data_products';
    protected $dbName = 'products_data';


    /**
     * 获取薪资宝产品
     * 
     * @access public
     * @return array
     */
    public function getXzbProduct(){
        /*
         *$where = "productsitem.pdid=product.id and product.isshow=1 and (product.status=2 or product.status=3) and product.exp_pType=1";
         *$result = $this->table(array('products_data.data_products'=>'product', 'products_data.data_productsitem'=>'productsitem'))->where($where)->order('product.id desc')->find();
         *return $result; 
         */
        $where = "data_products.isshow=1 and (data_products.status=2 or data_products.status=3) and data_products.exp_pType=1";
        $result = $this->join('LEFT JOIN products_data.data_productsitem on data_productsitem.pdid=data_products.id')
            ->where($where)
            ->order('data_products.id desc')
            ->find();
        return $result;
    }

    /**
     * 薪资宝：计算计划明细  
     * 
     * @param mixed $periodOrder 当前计划投资订单
     * @access public
     * @return array 
     */
    public function jisuanPeriodJihua($periodOrder){
        // 总本金, 总利息, 未开始期数, 本计划的全部期数, 还款账单
        $re = array('alltotalBj'=>0,'alltotalLx'=>0,'noYetNumber'=>0,'allPeriod'=>array(),'billstable'=>array());
        if($periodOrder[0]){
            $nowdate = strtotime(date("Y-m-d",time()));
            $nowdatem = strtotime(date("m",time()));
            //获取还款账单
            $forderid = $periodOrder[0]['period_first_orderid']>0?$periodOrder[0]['period_first_orderid']:$periodOrder[0]['id'];
            $billsModel = new BillsModel;
            $billstable = $billsModel->getBillstable($forderid);
            // 总本金
            $alltotalBj = 0;
            // 本计划的全部期数
            $allPeriod = array();
            $f_invest_date = $periodOrder[0]['indate']; // 提交时间
            // 获取薪资宝的年利率
            $prodModel = new ProductsModel;
            $yly = $prodModel->get_yly($periodOrder[0]['pdid'], $periodOrder[0]['money'], $periodOrder[0]['investtype'], $periodOrder[0]['investtime']);
            //是否已经投了最后一期
            $islast = false;
            for ($i = 1; $i <= $periodOrder[0]['investtime']; $i++){
                // 当期有投资
                foreach ($periodOrder as $key => $porder){
                    if($i == $porder['period_item']){
                        $alltotalBj += $porder['money'];
                        if(!isset($allPeriod[$i])){ // 本期投资计划
                            $allPeriod[$i] = $porder;
                        }else{
                            // 认购金额
                            $allPeriod[$i]['money'] += $porder['money'];
                            // 实际认购金额
                            $allPeriod[$i]['accountmoney'] += $porder['accountmoney'];
                            $allPeriod[$i]['totallx'] += $porder['totallx']; // 利息总额
                        }
                        // 投资额
                        $allPeriod[$i]['is_all_money'] +=  $porder['money'];
                        if($porder['paystatus'] == 2){ // 已确认
                            $allPeriod[$i]['is_status'] = 1; // 已投资
                            $allPeriod[$i]['is_status_title']='<em class="orange">已投资</em>'; 
                        }elseif($porder['paystatus'] == 0){ // 未到账
                            $allPeriod[$i]['is_status'] = 0; //代付款
                            $allPeriod[$i]['is_status_title'] = '待付款';
                            // 未付款订单号
                            $allPeriod[$i]['is_nopay_ordernum'] = $porder['ordernum'];
                        }
                        $allPeriod[$i]['is_yly'] = $yly; // 年利率
                    }
                    // 判断是否是最后一期
                    if($porder['period_item'] == $periodOrder[0]['investtime']){
                        $islast = true;
                    } 
                } /** end foreach*/
                // 如果不是第一期，就要加上上次的投资额
                if ($i > 1){
                    $allPeriod[$i]['is_all_money'] += $allPeriod[$i-1]['is_all_money'];
                }
                // 没有投资的
                if(!isset($allPeriod[$i]['is_yly'])){
                    $allPeriod[$i]['is_yly'] = 0.04;
                    $allPeriod[$i]['is_all_money'] = $allPeriod[$i-1]['is_all_money'];
                    $allPeriod[$i]['money'] = 0;
                    $allPeriod[$i]['is_status'] = 2; // 未开始
                    $allPeriod[$i]['is_status_title'] = '未开始'; 
                }
                // 如果是最后一期, 利息加1%
                if($islast){
                    $allPeriod[$i]['is_yly'] += 0.01;
                }
                //如果超过28号，下期投资时间为28号
                $allPeriod[$i]['is_invest_date'] =  strtotime('+' . ($i-1) . ' month', $f_invest_date);
                $nextMonth = date("m",strtotime('+' . ($i) . ' month', $f_invest_date));
                // 获取月份和日期
                $data_tmp = get_month_date($f_invest_date);
                if($i > 1 && $data_tmp['d'] > 28){
                    $is_invest_date_m = date("m", $allPeriod[$i]['is_invest_date']);
                    // 处理2月(特殊)
                    if($is_invest_date_m == $nextMonth){
                        $allPeriod[$i]['is_invest_date'] = strtotime(date("Y-".($nextMonth-1)."-28",$allPeriod[$i]['is_invest_date']));
                    }else{
                        $allPeriod[$i]['is_invest_date'] = strtotime(date("Y-m-28",$allPeriod[$i]['is_invest_date']));
                    }
                }else{
                    if($data_tmp['d']){
                        $allPeriod[$i]['is_invest_date'] = strtotime(date("Y-m-d",$allPeriod[$i]['is_invest_date']));
                    }
                }
                if(!isset($allPeriod[$i]['ordernum']) && ($nowdate > $allPeriod[$i]['is_invest_date'])){
                    $allPeriod[$i]['is_status'] = 3; //'已逾期';
                    $allPeriod[$i]['is_status_title'] = '已逾期';
                    $re['oldNumber']++;
                }
                if($nowdate == $allPeriod[$i]['is_invest_date'] && $allPeriod[$i]['is_status']!=0){
                    $allPeriod[$i]['is_status'] = 4; //'投资中';
                    $allPeriod[$i]['is_status_title'] = '投资中';
                }
                //上期投资
                if($allPeriod[$i]['is_status']==1 || $allPeriod[$i]['is_status']==4|| $allPeriod[$i]['is_status']==0){
                    $re['lastInvestNumber'] = $i;
                    $re['lastInvestDate']   = $allPeriod[$i]['is_invest_date'];
                    if($allPeriod[$i]['money']>0){
                        $re['lastInvestMoney']  = $allPeriod[$i]['money'];
                    }
                }
                //第一比投资金额
                if($i == 1){
                    $re['maxInvestMoney']  = $allPeriod[$i]['money'];
                }
                //下次投资时间
                if(($allPeriod[$i]['is_status']==2) && !isset($re['nextInvestDate']) ){
                    $re['nextInvestDate']   = $allPeriod[$i]['is_invest_date'];
                    $re['nextInvestNumber'] = $i;
                }
                if($nowdate == $allPeriod[$i]['is_invest_date'] && $i==$periodOrder[0]['investTime']){
                    $re['nextInvestDate']   = $allPeriod[$i]['is_invest_date'];
                    $re['nextInvestNumber'] = $i;
                }
                //投资中
                if($allPeriod[$i]['is_status']==4 || $allPeriod[$i]['is_status']==0){
                    $re['nowInvest']  = $i;
                }
                //未开始期数
                if($allPeriod[$i]['is_status']==2){
                    $re['noYetNumber']++;
                }
            }/** end for*/
            //计算总收益和每期收益
            //已经计息天数
            $allorderTimeDays = 0;
            //总利息
            $alltotalLx = 0;
            foreach ($allPeriod as $i=>&$prouctInfo){
                // 总利息和本期
                $prouctInfo['totallx'] = $prouctInfo['is_all_money'] * $prouctInfo['is_yly'] / 12;
                $prouctInfo['totallx'] = sprintf("%.2f", $prouctInfo['totallx']);
                $alltotalLx += $prouctInfo['totallx'];
            }
            //上期利息
            $re['lastInvestLx']     = $allPeriod[$re['lastInvestNumber']]['totallx'];
        }
        $re['alltotalBj']  = $alltotalBj; // 总本金
        $re['alltotalLx']  = $alltotalLx; // 总利息
        $re['allPeriod']   = $allPeriod; // 本计划的全部期数
        $re['billstable']  = $billstable; // 还款账单

        //p($re); 
        return $re;
    }

    /**
     * getPeriodNumber 
     * 获取用户的薪资宝投资计划数
     * 
     * @param mixed $userid 用户id
     * @access public
     * @return array
     */
    public function getPeriodNumber($userid){
        $res = array();
        $where['userid'] = $userid;
        $where['payStatus'] = array('in', array(0, 2));
        $where['exp_order_type'] = 1;

        $result = $this->table('order_data.data_orders')
            ->field('period_number')
            ->where($where)
            ->group('period_number')
            ->select();
        if($result){
            foreach ($result as $v){
                $res [] = $v['period_number'];
            }
        }
        return $res;
    }

    /**
     * getLicaiInvestmentPlan
     * 定时投资计划 
     * 
     * @param array $where 查询条件 
     * @access public
     * @return array 计划数对应的计划任务
     */
    public function getLicaiInvestmentPlan($where){
        $rearray = array();
        $result = $this->table('log_data.data_licai_investment')
            ->where($where)
            ->order('period_number asc, id asc')
            ->select();
        if($result){
            foreach ($result as $v){
                $rearray[$v['period_number']] = $v;
            }
        }
        //p($rearray);
        return $rearray;
        exit;
    }

    /**
     * getOneLicaiInvestmentPlan
     * 获取用户某个定时投资计划 
     * 
     * @param mixed $uid 用户id
     * @param mixed $period_number 计划数
     * @access public
     * @return array
     */
    public function getOneLicaiInvestmentPlan($uid,$period_number){
        $where['uid'] = $uid;
        $where['exp_order_type'] = 1;
        $where['period_number'] = $period_number;

        $result = $this->table('log_data.data_licai_investment')
            ->where($where)
            ->find();
        return $result;
    }

    /**
     * editLicaiInvestmentPlan
     * 修改薪资宝定时投资计划 
     * 
     * @param mixed $id 定时投资计划id
     * @param array $data 修改的数据
     * @access public
     * @return BOOL
     */
    public function editLicaiInvestmentPlan($id,$data){
        $result = $this->table('log_data.data_licai_investment')
            ->where('id='.$id)
            ->save($data);
        return $result;
    }

    /**
     * addLicaiInvestmentPlan
     * 薪资宝添加定时投资记录 
     * 
     * @param array  $data 
     * @access public
     * @return int 返回插入记录id
     */
    public function addLicaiInvestmentPlan($data){
        return  $this->table('log_data.data_licai_investment')
            ->data($data)
            ->add();
    }

}
