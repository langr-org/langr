<?php
/**
 * @file OrdersController.class.php
 * @brief 订单相关
 * 
 * Copyright (C) 2016 ZKC.com
 * All rights reserved.
 * 
 * @package Controller
 * 
 * $Id: OrdersController.class.php 62785 2016-07-04 05:47:32Z huanghua $
 */

namespace Home\Controller;

use Think\Controller; // 测试
use Home\Controller\AppController;
use Home\Model\OrdersModel;
use Home\Model\BillsModel;
use Home\Model\OrdersBillViewModel;
use Home\Model\ProductXzbModel;
use Home\Model\CouponModel;
use Home\Model\CoinsModel;

class OrdersController extends AppController 
//class OrdersController extends Controller 
{
	/**
	 * @brief 用户认购订单列表
	 * @param 
	 *!@param appid
	 * @return 
	 * {
	 * 	"orders":[{},{}],
	 * }
	 */
	public function index() /* {{{ */
	{
		/* 当前授权用户信息： */
		/*
		 *$token = self::$current_token;
		 *$uid = self::$current_uid;
		 *$appid = self::$current_appid;
		 */

		/* 客户端传递的参数在：$this->args; */
		/* TODO: ... */
		//$orders = D('Orders')->test();exit;
		$uid = self::$current_uid;
		//$map['userid'] = 204865;
		$map['userid'] = $uid;

		/* 查询已经付款的订单 */
		$map['paystatus'] = array('in', '0, 2, 5');
		$exp_order_type = '0,3';								/* 1薪资宝订单2省心宝3体验标 */
		$type = $_GET['type'] ? intval($_GET['type']) : 0;		/* 产品分类 */
		$month = $_GET['month'] ? intval($_GET['month']) :0;	/* 最近月份 */
		/* 投资类型 */
		$investStatus = $_GET['investstatus'] ? intval($_GET['investstatus']) : 0;
		//$export = $_GET['export'] ? true : false;		/* 预留 */
		$timeStart = $_GET['timestart'];				/* 查询开始时间 */
		$timeEnd = $_GET['timeend'];					/* 查询结束时间 */
		/* 分页 */
		$page = $_GET['page'] ? intval($_GET['page']) : 1;
		$pagesize = 9; /* 测试 默认=9 */
		$offset=($page-1)*$pagesize;

		/* 条件筛选 */
		if (!empty($month)) {		/* 最近月份 */
			$month_array = array(1,3,6);
			if (in_array($month, $month_array)) {
				$start = strtotime(date('Y-m-d',time()))-2592000*$month;	/* 2592000为30天秒数 */
				$end = time();
				$map['indate'] = array(array('egt',$start),array('elt',$end));
			}
		} else {					/* 时间 */
			$start = strtotime($timeStart);
			$end = strtotime($timeEnd);
			if ($timeStart && $timeEnd) {
				$map['indate'] = array(array('egt', $start), array('elt', $end+86400));
			} else if ($timeStart) {
				$map['indate'] = array('egt', $start);
			} else if ($timeEnd) {
				$map['indate'] = array('elt', $end+86400);
			}
		}
		/* 产品分类查询*/
		if ($type) {
			$type_array = array(1,2,3,4,5);
			if (in_array($type, $type_array)) {
				if ($type == 4) {		/* 大于20为复利 */
					$map['ptype'] = array('in', array("21","22","23","24","27",
						"28","29","30","31","32","33")
					);
				} else if ($type == 5) { /* 供应链系列 */
					$map['ptype'] = 1;
					$map['isgyl'] = 1;
				} else if ($type == 1) {
					$map['ptype'] = 1;
					$map['isgyl'] = 0;
				} else {
					$map['ptype'] = $type;
				}
			} else if ($type == 6) {	/* 理财计划 */
				$licaitype = 1;
			}
		}

		/* 理财标的 */
		if ($exp_order_type) {
			if (isset($licaitype) && $licaitype == 1) {
				$map['_string'] = "(( orders.exp_order_type=1 and orders.period_item=1 and orders.period_first_orderid=0) or (orders.exp_order_type=2 and orders.order_type<>1))"; 
			} else {
				//$map['exp_order_type'] = array('in', $exp_order_type);
				$map['_string'] = "( orders.exp_order_type in(".$exp_order_type.") or ( orders.exp_order_type=1 and orders.period_item=1 and orders.period_first_orderid=0) or (orders.exp_order_type=2 and orders.order_type<>1))"; 
			}
		}

		/* 投资类型 */
		if ($investStatus == 1) {		/* 投资中的订单 */
			$map['_string'] = $map['_string'] . ' AND (billstable.isReturn=0 OR (orders.order_type=1 AND orders.redeem=0))';
			$orders = D('OrdersBillView')->distinct(true)->where($map)->order('id desc')->limit($offset, $pagesize)->select();
			//echo  D("OrdersBillView")->getLastSql(),'<br>';
			$total = D("OrdersBillView")->where($map)->order('id desc')->count('DISTINCT orders.id');
			//echo  D("OrdersBillView")->getLastSql();
		} else if ($investStatus == 2) {/* 查询投资完成的订单 */
			/* 获取未还款的订单id的账单 */
			$norepayment_ids = D('Bills')->get_norepayment_ids($map['userid']);
			if ($norepayment_ids) {
				$map['id'] = array('not in', $norepayment_ids);
				$map['_string'] = $map['_string'] . ' AND billstable.month = billstable.maxmonth';
				$map['isReturn'] = array('in',array(1,3));

				$orders = D('OrdersBillView')->where($map)->order('id desc')->limit($offset, $pagesize)->select();
				//echo  D("OrdersBillView")->getLastSql();
				$total = D("OrdersBillView")->where($map)->order('id desc')->count();
			}
		} else {	/* 转让 */
			if ($investStatus == 3) {	/* 转让中 */
				$map['redeem'] = 2;
			} else if ($investStatus == 4) { /* 转让完成 */
				$map['redeem'] = 3;
			} else {					/* 查询所有 */
				$orders = D("OrdersView")->where($map)->order('id desc')->limit($offset, $pagesize)->select();
				//echo  D("OrdersView")->getLastSql(); 
				$total = D("OrdersView")->where($map)->order('id desc')->count();
			}
		}

		/* 数据处理 */
		$oarr1 = array();
		$oarr2 = array();
		$curentTime = strtotime(date("Y-m-d", time()));
		/* 获取最新的薪资宝产品 */
		$xzb_product = D('ProductXzb')->getXzbProduct();
		/* 薪资宝定时投资任务 */
		$invPlanWhere['uid'] = $map['userid'];
		$invPlanWhere['exp_order_type'] = 1;	/* 薪资宝 */
		$invPlan = D('ProductXzb')->getLicaiInvestmentPlan($invPlanWhere);
		//p($xzb_product);
		foreach ($orders as &$item) {
			$oarr1[] =  $item['id'];
			/* 订单类型,0是固定1复利2固定+浮动3纯浮动4固定无担保 */
			if ($item['order_type'] != 1) {
				$item['transfer'] = 0; /* 默认设定为不可以转让 */
				$time = time();
				/* 0:改产品不能转让;1:该产品可转让 */
				if ($item['istransfer'] == 1 && $item['investtype'] == 'month' && $item['investtime'] > 5 && $item['accountmoney'] >= 1000) {
					/* 获取订单的未还款账单 */
					$billsunreturn = D('Bills')->get_billstable_unreturn($item['id'],$item['userid']); 
					//p($billsunreturn);
					//echo  D("Bills")->getLastSql();exit;
					/* 账单前转让时间 */
					$trtime = ($billsunreturn['moneybj'] >= 0.01) ? 86400*7 : 86400;
					if (empty($item['transfer_difftime'])) {
						$item['transfer_difftime'] = '{"6":15,"9":20,"12":30,"18":45,"24":60,"36":90}';
					}
					$tzzrarr = @json_decode($item['transfer_difftime'], 1);
					$tzday = $tzzrarr[$item['investtime']];
					if (empty($tzday)) {
						$tzday = 90;
					}
					$tztime = $tzday * 86400;	/* 产品转让期限 */
					$lctime = strtotime(date("Y-m-d",time()));	/* 今天凌晨时间 */
					/* 持有XX天条件是否能通过 */
					$tj = !empty($item['zrtime']) ? ($lctime - strtotime(date("Y-m-d",$item['zrtime'])) >= $tztime):($lctime - $item['interestdate'] >= $tztime);
					if ($billsunreturn['returndate'] && ($billsunreturn['returndate'] - $lctime > $trtime) && $tj) {
						/* redeem 0:未赎回;1:已赎回;2:转让中;3:转让成功 */
						if ($item['redeem'] == 2) {		/* 转让中 */
							$item['transfer'] = 2;
						} else if ($item['redeem'] == 3) {	/* 转让成功 */
							$item['transfer'] = 3; 
						} else {
							$item['transfer'] = 1;		/* 可以转让 */
						}
					} else {
						if ($item['redeem'] == 0 && ($billsunreturn['returndate'] || ($billsunreturn['returndate'] - $lctime > $trtime))) {
							if (!$tj) {
								$item['diff'] = empty($item['zrtime']) ? date('Y-m-d', $item['interestdate'] + $tztime):date('Y-m-d', $item['zrtime'] + $tztime);
							} else {
								$item['diff'] = date('Y-m-d', $billsunreturn['returndate'] + 86400);
							}
						}
					}
				}
				/* 薪资宝 */
				if ($item['exp_order_type'] == 1) {
					/* 当前计划投资订单 */
					$periodOrder = D('OrdersView')->getPeriodOrder($item['userid'],$item['period_number']);
					//p($periodOrder);//exit;
					/* 计算计划明细 */
					$periodJihua = D('ProductXzb')->jisuanPeriodJihua($periodOrder);
					//p($periodJihua);
					$item['accountmoney'] = $periodJihua['billstable']['moneybj'];
					$item['noYetNumber'] = $periodJihua['noYetNumber'];
					$item['nextInvestDate'] = $periodJihua['nextInvestDate'];
					$item['lastInvestNumber'] = $periodJihua['lastInvestNumber'];
					$item['lastInvestMoney'] = $periodJihua['lastInvestMoney'];
					$item['lastInvestLx'] = $periodJihua['lastInvestLx'];
					$item['maxInvestMoney'] = $periodJihua['maxInvestMoney'];
					$item['nowInvest'] = $periodJihua['nowInvest'];
					$item['runday'] = date('d',$periodJihua['allPeriod'][2]['is_invest_date']);
					$item['step'] = $xzb_product['oneaddmoney']*10000; //累加
					$item['min']  = $xzb_product['beginmoney'];
					$item['max']  = $periodJihua['maxInvestMoney']*3;

					$item['totallx'] = 0;
					foreach ($periodJihua['allPeriod'] as $key=>$v) {
						if ($v['is_status'] == 4 && $v['money'] >0) {
							$item['min'] = 100;
							$item['max'] = $item['max'] - $v['money'];
						}
						if ($key <= $periodJihua['lastInvestNumber'] || $v['is_status']==3 || $v['is_status']==4) {
							$item['totallx'] +=$v['totallx'];
						}
					}
					if ($item['noYetNumber'] == 11) {
						$item['max'] = 0;
					}
					/* 自动转投金额 */
					$item['autoinvestprice'] = $periodJihua['lastInvestMoney'];
				}
			} else if ($item['order_type'] == 1) {	/* 1:复利 */
				/* currprincipal:复利宝当期本金 */
				$item['totallx'] = $item['currprincipal'] + $item['totallx'] - $item['accountmoney'];
				$laveDay = $maxQx = 0;	/* 期数 */
				/* 离下期还有几天  compoundtime:复利宝 复利时间 */
				$laveDay = intval(ceil($item['compoundtime'] - $curentTime) / 86400);
				$item['laveday'] = $laveDay;
				if ($item['investtype'] == 'month') {	/* month按月算 */
					/* 总期数 */
					$maxQx = intval(ceil(($item['rgendtime'] - $item['interestdate']) / ($item['investtime'] * 86400 * 30)));
					/* 上一期的结束时间 currperiod:当期期数 */
					$prevDate = ($item['currperiod'] ==1 ) ? $item['interestdate'] :
						strtotime("+" . $item['investtime'] * ($item['currperiod'] - 1) . " month", $item['interestdate']); 
				} else if ($item['investtype'] == 'day') {	/* day按日算 */
					/* 总期数 */
					$maxQx = intval(ceil(($item['rgendtime'] - $item['interestdate']) / ($item['investtime'] * 86400)));
					/* 上一期的结束时间 */
					$prevDate = ($item['currperiod'] ==1 ) ? $item['interestdate'] : 
						strtotime("+" . $item['investtime'] * ($item['currperiod'] - 1) . " day", $item['interestdate']);
				}
				/* 本期天数*本期利息  + 本期本金 */
				$item['last_lx'] = ($item['compoundtime'] - $prevDate) * $item['yly'] / 365 / 100 + $item['currprincipal'];
				$item['maxqx'] = $maxQx;	/* 最大期数 */
				/* 已经还款的个数 */
				$count = D('Bills')->is_return_count($item['id'], $item['pdid']);
				/* 还款总数是否总等于订单表中的最后一期数 */
				$item['isover'] = ($count == 1) ? 1 : 0;
				if ($item['isover'] == 0 && $item['redeem'] == 0) {
					$oarr2[] = $item['id'];
				}
				/* 是否可以继续复投 (获取未还款的订单) */
				$billstableList =D('Bills')->get_billstable_unreturn($item['id'],$item['userid']);
				$nowtime = time();	/* 当前 */
				if ($billstableList['returndate'] && $nowtime < $billstableList['returndate']) {
					/* 不是省心宝中的季盈宝 */
					if ($item['exp_order_type'] != 2) {
						$item['cantouzi'] = 1;
					}
				}
			} else if ($item['order_type'] == 2) { /* 2:固定+浮动 */
				/* 根据用户ID和订单ID获取还款账单表中的总利息 */
				$item['totallx'] = D('Bills')->getRealFloatLX($item['userid'], $item['id']);
			} else if ($item['order_type'] == 3) { /* 3:纯浮动 */
				$item['totallx'] = D('Bills')->getRealFloatLX($item['userid'], $item['id']);
			} else if ($item['order_type'] == 4) { /* 4:固定无担保 */
				$item['totallx'] = D('Bills')->getRealFloatLX($item['userid'], $item['id']);
			}
			/* 获取是否使用代金券 */
			$coupon = D('Coupon')->get_coupon_by_ordernum($item['userid'], $item['ordernum']);
			if (is_array($coupon)) {
				$item['coupon_price'] = $coupon['price'];
			}
			/* 是否使用加息券 */
			$jiaxijuan =  D('Coupon')->get_jiaxijuan_by_ordernum($item['userid'], $item['ordernum']);
			if (is_array($jiaxijuan)) {
				$item['jiaxijuan_price'] = $jiaxijuan['interest'];
			}
			/* 是否使用了88币 */
			$coins = D('Coins')->getProductCoins($item['userid'], $item['ordernum']);
			$item['coions_tradeNum'] = $coins ? $coins['tradenum'] : 0;
			/* 重阳感恩季 暖心好礼来孝亲——投资即送高达90万保额的孝心保单 */
			/** TODO */
			$cyjlog = D('Orders')->get_cyj_order($item['ordernum'], $map['userid']);
			$etime = strtotime('2015-10-31 23:59:59');
			$nowtime = time();
			if ($cyjlog && $nowtime <= $etime) {
				$item['cyjtype'] = $cyjlog['type'];
			}
			if ($cyjlog && $nowtime > $etime) {
				$item['cyjold'] = 1;
			}
			/* END 重阳感恩节 */
			/* 省心宝 */
			if ($item['exp_order_type'] ==2) {
				$planinfo = D('Orders')->get_ordersplan_info($item['id']);
				$item['poid'] = $planinfo['id'];	/* 投资计划订单id */
				$item['pdid'] = $planinfo['pdid'];	/* 产品ID-理财计划产品表 */
				$item['productname'] = $planinfo['pname'];
				$item['accountmoney'] = $item['money'] = $planinfo['money'];
				$item['yly'] = $planinfo['yield'];
				$item['totallx'] = $planinfo['totallx'];
				$item['ordernum'] = $planinfo['ordernum'];	/* 订单号 */
				$item['paystatus'] = $planinfo['paystatus'];
				$item['planinfo'] = $planinfo;
			}
		}

		$data = array(
			'page' => $page,
			'total' => $total,
			'invPlan' => $invPlan,	/* 薪资宝定时任务 */
			'data' => $orders
		);
		return $this->_return($data);
	} /* }}} */


	/**
	 * 定投系列和复利系列 -- 订单详情
	 * 
	 * GET 请求
	 * 参数：
	 * orderid : 订单id
	 * ptype：产品类型 1：定投 2：>20 为复利系列(废弃)
	 * 
	 * @access public
	 * @return array
	 */
	public function detail() /*{{{*/
	{
		//$uid = $map['userid'] = 204865; // 测试
		$uid = self::$current_uid;
		$orderid = $_GET['orderid'] ? intval($_GET['orderid']) : 0;	/* 产品分类 */
		//$ptype = $_GET['ptype'] ? intval($_GET['ptype']) : 0;	/* 产品分类 */
		$page = $_GET['page'] ? intval($_GET['page']) : 1;
		$pagesize = 200; 

		$order = D('OrdersView')->get_order_byid($uid, $orderid);
		if (empty($order)) {
			/* TODO... */
			return $this->_return(self::_error(440, '无效的订单id'));
			/* 这里有错误处理 */
		}
		if ($order['ptype'] == 1) { /* 返回定投数据 */
			// http://api.88.com.cn/orders/detail?ptype=1&orderid=95010
			//$order = D('OrdersView')->get_order_byid($uid, $orderid);
			/* 已经还款的个数 */
			$count = D('Bills')->is_return_count($order['id'], $order['pdid']);
			/* 还款总数是否总等于订单表中的最后一期数 */
			$order['isover'] = ($count == 1) ? 1 : 0;

			/* 获取还款明细 */
			$where['orderid'] = $order['id'];
			$where['pdid'] = $order['pdid'];
			$where['isReturn'] = array('elt',4);
			$where['userid'] = $order['userid'];
			$list = D('Bills')->get_dt_return_list($where, $page, $pagesize);

			if ($list) {
				foreach ($list as &$item) {
					$item['moneysh'] = $item['moneybj'] + $item['moneylx']; /* 总收益 */
					$order['moneyBjTotal'] += $item['moneybj']; /* 总本金 */
					$order['moneyLxTotal'] += $item['moneylx']; /* 总利息 */
					if ($item['isreturn'] == 1) {
						$order['moneyWsTotal'] += 0;			/* 未收 */
						$order['moneyShTotal'] += $item['moneybj'] + $item['moneylx']; /* 已收 */
					} else {
						$order['moneyShTotal'] += 0;
						$order['moneyWsTotal'] += $item['moneybj'] + $item['moneylx'];
					}
				}
			} /* end $list */
			/* 查询未付款订单 */
			$oarr = D('Bills')->sel_non_payment($orderid);
			if (count($oarr)>0) {
				$non_papyment = 1;	/* 订单未付款 */
			}

			/* 获取还款明细个数 */
			$total = D('Bills')->get_dt_return_list_count($where);

			$where2['orderid'] = $order['id'];
			$where2['pdid'] = $order['pdid'];
			$where2['isReturn'] = array('eq',1);
			$where2['userid'] = $order['userid'];
			/* 获取已还款明细个数 */
			$returnTotal = D('Bills')->get_dt_return_list_return_count($where2);
			$sytotal = $total - $returnTotal;		/* 剩余还款明细 */

			/* 是否使用88币 */
			$coinsArr = D('Coins')->getProductCoins($order['userid'], $order['ordernum']);
			/* 是否使用收益增值劵 */
			$usejxj = D('Coupon')->get_jiaxijuan_by_ordernum($order['userid'], $order['ordernum']);
			//p($usejxj);
			$data = array(
				'page' => $page,
				'order' => $order,
				'coins88' => $coinsArr['tradenum'],	/* 使用88 币个数 */
				'userjxj' => $usejxj,
				'list' => $list,
				'total' => $total,					/* 还款明细个数 */
				'non_papyment' => $non_papyment,	/* 未还款 */
				'sytotal' => $sytotal,				/* 剩余期数 */
				'returnTotal' => $returnTotal,
			);
			return $this->_return($data);
		} else if ($order['ptype'] > 20) { /* 返回复利数据 */
			//exit('复利产品..');
			//p($order);
			if ($order['order_type'] != 1) {
				/* TODO..... */
				//exit('不是复利产品');
				return $this->_return(self::_error(441, '不是复利产品'));
			}
			$total = $order['currperiod']; /* 当期期数:用户当前最大期数 */
			/* 获取是否使用88币 */
			$coinsArr = D('Coins')->getProductCoins($order['userid'], $order['ordernum']);  
			/* 是否使用加息劵 */
			$usejxj = D('Coupon')->get_jiaxijuan_by_ordernum($order['userid'], $order['ordernum']); 
			//p($usejxj);
			/* 获取复利宝详细期数 */
			$list = D('Orders')->flb_earnings_detail($page, $pagesize, $order, $coinsArr['tradenum'], $usejxj);
			//p($list);
			$data = array(
				'page' => $page,
				'total' => $total,
				'coins88' => $coinsArr['tradenum'],/* 使用88 币个数 */
				'order' => $order, /* 订单信息 */
				'usejxj' => $usejxj,
				'list' => $list, /* 期数列表 */
			);

			return $this->_return($data);
		} else {
			return $this->_return(self::_error(442, '产品类型错误'));
		}
	} /*}}}*/

	/**
	 * 停止复投 
	 * POST
	 * param
	 * orderid 订单id
	 * http://api.88.com.cn/orders/stop_invest?orderid=95662
	 *
	 * @access public
	 * @return void
	 */
	public function stop_invest() /*{{{*/
	{
		//$uid = $map['userid'] = 204865; // 测试
		//$orderid = I('get.orderid', 0) + 0; // 测试
		$uid = self::$current_uid;
		$orderid = I('post.orderid', 0) + 0;
		
		$order = D('OrdersView')->get_order_byid($uid, $orderid);
		//p($order);
		if (empty($order) || $orderid <1) {
			/* TODO */
			return $this->_return(self::_error(440, '无效的订单id'));
		}
		if ($order['order_type'] != 1) {
			return $this->_return(self::_error(441, '不是复利产品'));
		}
		if ($order['paystatus'] != 2) {
			/* TODO */
			return $this->_return(self::_error(442, '订单未付款'));
		}
		/* 判断时间是否是3天前 */
		$curentTime = time();
		if ($order['compoundtime'] < ($curentTime + 86400 * 3)) {
			/** 重构, 不知道是什么鬼 ... */    
		}
		/* 本期还款账单是否已经写入 */
		$is_haved_billstable = D('Bills')-> is_haved_billstable($orderid, $order['userid']);
		//p($is_haved_billstable);
		if ($is_haved_billstable) {
			//exit('已设置停止复投');
			return $this->_return(self::_error(443, '已设置停止复投'));
		}
		/* 更新订单表赎回状态：redeem=1, operate=time() */
		$update_data['redeem'] = 1;
		$update_data['operate'] = time();
		$return = D('Orders')->where('id='. $orderid)->save($update_data);
		/* 状态设置成功 */
		if ($return) {
			/* 插入最后一期还款账单 */
			$billstableData['pdid'] = $order['pdid'];
			$billstableData['userid'] = $order['userid'];
			$billstableData['orderid'] = $order['id'];
			$billstableData['publishid'] = $order['pduserid']; /* 产品发布者的用户ID */
			$billstableData['month'] = $order['currperiod']; /* 当期期数 */
			$billstableData['maxmonth'] = $order['currperiod']; /* 最大期数 */
			$billstableData['moneyBj'] = $order['currprincipal']; /* 本金 */
			$billstableData['moneyLx'] = $order['totallx'];
			$billstableData['returnDate'] = $order['compoundtime'];
			$billstableData['mypayStatus'] = 1;
			$billstableData['isReturntime'] = "";
			$billstableData['isReturn'] = 0;
			$billstableData['confirmMan'] = '';
			$billstableData['confirmTime'] = 0;
			$billstableData['inDate'] = time();
			$billstableData['consoleuName'] = '';
			$billstableData['consoleuTime'] = 0;
			$billstableData['descstr'] = '';
			$billstableData['yqdays'] = 0;
			$billstableData['yqfx'] = 0.00;
			/* 插入数据 */
			$return_billstable = D('Bills')->data($billstableData)->add();
			if (!$return_billstable) {
				/** TODO */
				//exit('写入账单失败...');
				return $this->_return(self::_error(444, '写入账单失败'));
			}
		/* endif $return */
		} else {
			/* TODO */
			//exit('订单设置失败');
			return $this->_return(self::_error(445, '订单修改状态失败'));
		}

		//exit('设置成功');
		return $this->_return(self::_error(self::E_OK, '设置成功'));
	} /*}}}*/

	/**
	 * 设置继续复投 
	 *
	 * POST
	 * param
	 * orderid 订单id
	 *
	 * http://api.88.com.cn/orders/continue_invest?orderid=95662
	 * 
	 * @access public
	 * @return void
	 */
	public function continue_invest() /*{{{*/
	{
		//$orderid = I('get.orderid', 0) + 0; //测试
		//$uid = $map['userid'] = 204865; // 测试
		
		$uid = self::$current_uid;
		$orderid = I('post.orderid', 0) + 0;
		/* 获取用户的订单信息 */
		$order = D('OrdersView')->get_order_byid($uid, $orderid);
		//p($order);
		if (empty($order) || $orderid <1) {
			/* TODO */
			return $this->_return(self::_error(440, '无效的订单id'));
		}
		if ($order['order_type'] != 1) {
			return $this->_return(self::_error(441, '不是复利产品'));
		}
		if ($order['paystatus'] != 2) {
			return $this->_return(self::_error(442, '订单未付款'));
		}
		/* 本期还款账单是否已经写入 */
		$is_haved_billstable = D('Bills')->is_haved_billstable($orderid, $order['userid']);
		//p($is_haved_billstable);
		if (!$is_haved_billstable) {
			/* TODO */
			return $this->_return(self::_error(446, '账单不存在'));
		}
		$nowtime = time(); /* 当前 */
		if ($nowtime >= $is_haved_billstable['returndate']) {
			return $this->_return(self::_error(447, '不能继续复投'));
		}
		/* 查看是否使用了88币 */
		$coins = D('Coins')->getProductCoins($uid, $order['ordernum']);
		if ($coins['tradeNum']>0) {
			/* TODO */
			return $this->_return(self::_error(448, '使用了88币，不能继续复投'));
		}
		/* 更新订单表赎回状态：redeem=0, operate=time() */
		$update_data['redeem'] = 0;
		//$update_data['operate'] = time();
		$return = D('Orders')->where('id='. $orderid)->save($update_data);
		/* 状态设置成功 */
		if ($return) {
			/* 插入最后一期还款账单 */
			$billstableData['pdid'] = 0;
			$billstableData['userid'] = 0;
			$billstableData['orderid'] = 0;
			$billstableData['publishid'] = 0;	/* 产品发布者的用户ID */
			$billstableData['month'] = 0;		/* 当期期数 */
			$billstableData['maxmonth'] = 0;	/* 最大期数 */
			$billstableData['moneyBj'] = 0;		/* 本金 */
			$billstableData['moneyLx'] = 0;
			$billstableData['returnDate'] = 0;
			$billstableData['mypayStatus'] = 1;
			$billstableData['isReturntime'] = time();
			$billstableData['isReturn'] = 2;
			$billstableData['confirmMan'] = '';
			$billstableData['confirmTime'] = 0;
			$billstableData['inDate'] = time();
			$billstableData['consoleuName'] = '';
			$billstableData['consoleuTime'] = 0;
			$billstableData['descstr'] = $order['id'];
			$billstableData['yqdays'] = 0;
			$billstableData['yqfx'] = 0.00;
			/* 更新数据 */
			$return_billstable = D('Bills')->where('id='.$is_haved_billstable['id'])->save($billstableData);
			//echo D('Bills')->getLastSql();
			if (!$return_billstable) {
				/* TODO */
				return $this->_return(self::_error(449, '设置继续复投失败'));
			}
		 /* endif $return */
		} else {
			/* TODO */
			return $this->_return(self::_error(450, '设置继续复投，订单更新失败'));
		}
		return $this->_return(self::_error(self::E_OK, '设置成功'));
	} /*}}}*/
}

/* end file */
