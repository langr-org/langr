<?php
/**
 * @file ProductsController.class.php
 * @brief 
 * 
 * Copyright (C) 2016 ZKC.com
 * All rights reserved.
 * 
 * @package Controller
 * 
 * $Id$
 */

namespace Home\Controller;

use Think\Controller; // 测试
use Home\Controller\AppController;
use Home\Model\RulesModel;
use Home\Model\ProductsModel;
use Home\Model\CouponModel;

class ProductsController extends AppController
{
	public function index() /* {{{ */
	{
		/*switch ($_SERVER['REQUEST_METHOD']) {
		case 'GET' :
			if (isset($_GET['ac']) && $_GET['ac'] == 'sms') {
				$this->sms();
			} else if (isset($_GET['ac']) && $_GET['ac'] == 'image') {
				$this->image();
			}
			break;
		case 'POST' :
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
		
		return $this->_return(self::_error(self::E_METHOD));*/
		
		$this->product_list();
	} /* }}} */
	
	/**
	 * @brief 产品列表
	 * @param 
	 *!@param appid
	 * @return 
	 * {
	 * 	"list":[{},{}],
	 * }
	 */
	public function index2() /* {{{ */
	{
		/* 当前授权用户信息： */
		$token = self::$current_token;
		$uid = self::$current_uid;
		$appid = self::$current_appid;

		/* 客户端传递的参数在：$this->args; */
		/* TODO: ... */
		$data = array();

		return $this->_return($data);
	} /* }}} */

	/**
	 * product_list 
	 * 产品列表
	 * method: GET
	 *
	 * @access protected
	 * @return string/json
	 */
	protected function product_list()/*{{{*/
	{
		$products_model = new ProductsModel();
		$coupon_model = new CouponModel();

		/** TODO :条件查询*/
		$page = I('get.page', 1); /** 分页数 */
		$page_size = 20;
		$ptype = I('get.ptype'); /** 产品类型 */
		$vt = I('get.vt'); /** 投资期限 */
		$zq = I('get.zq'); /** 专区 */
		$where = ' ';

		if ($ptype == 1) { /** 复利系列 */
			$where .= "and data_products.pType > 20 ";
		} elseif ($ptype == 2) { /** 安享系列 */
			$where .= "and data_products.pType=1 and data_products.isgyl=1 ";
		} elseif ($ptype == 3) { /** 定投系列 */
			$where .= "and data_products.pType=1 and data_products.isgyl=0 ";
		}
		/** 投资期限(月) */
		if ($vt == 1) {
			$where .= 'and data_productsitem.day = 7 ';
		} elseif ($vt == 6) {
			$where .= "and data_productsitem.investTime >= 1 and data_productsitem.investTime <= 6 ";
		} elseif ($vt == 12) {
			$where .= "and data_productsitem.investTime >= 7 and data_productsitem.investTime <= 12 ";
		} elseif ($vt == 13) {
			$where .= "and data_productsitem.investTime > 12 ";
		}
		/** 专区 */
		if ($zq == 1) { /** 普通专区 */
			$where .= "and data_products.productName not like '%新手%' and (data_products.hd_msg IS NULL or data_products.hd_msg='') and data_products.istransfer != 1 ";
		} elseif ($zq == 2) { /** 新手专区 */
			$where .= "and data_products.productName like '%新手%' ";
		} elseif ($zq == 3) { /** 活动专区 */
			$where .= "and data_products.hd_msg!='' ";
		} elseif ($zq == 4) { /** 转让专区 */
			$where .= "and data_products.istransfer = 1 ";
		}
		/** 上线内产品 */
		$current_time = time();
		$where .= "and data_products.onlinetime <= {$current_time} ";
		$where .= "and data_products.offlinetime >= {$current_time} ";

		/** 获取产品信息 */
		if ($ptype == 4){
			$plist = array();
		} else {
			$plist = $products_model->getProducts($where);
		}
		/** 获取薪资宝 */
		$xzblist = $products_model->getProductXzb(541, '薪资宝');
		if (($zq!=4) && !empty($xzblist) 
			&& (empty($ptype) || $ptype==4)) {
			if(empty($vt) || $vt==12) {
				array_push($plist, $xzblist);
			}
		}
		/** 获取产品数 */
		$total_list = $products_model->getProductsCount($where);
		$is_count = empty($total_list)?0:1;
		$total_list = ($ptype == 4)? count($plist) : $total_list+$is_count;
		//p($plist);exit;
		$start = time();
		
		foreach ($plist as &$val) {
			/** 是否能使用88币*/
			$val['cancoins'] = $this->_can_use_coin88($val['id']);   
			/** 复利宝 */
			if ($val['ptype'] > 20) {
				$flbtype = $products_model->info_flbtype($val['ptype']);
				$val['productname'] = mb_substr($flbtype['flbtype'], 0, 3) . '&nbsp;' . $val['productname'];
			}
			/** 投资进度 */
			if ($val['totalamount'] > 0) {
				$val['bl'] = sprintf("%.2f", $val['totalmoney'] / $val['totalamount'] * 100);
			} else {
				$val['bl'] = 0;
			}
			/** 默认收益 */
			$bj = $val['beginmoney'];
			if ($val['day'] > 0) {
				$val['benjin_lx'] = sprintf("%.2f", $bj + ($bj * $val['hoperate1'] / 36500 * $val['day']));
			} else {
				if ($val['investtime']) {
					$end = strtotime("+" . $val['investtime'] . " month", $start);
					$days = ($end - $start) / 86400;
					$val['benjin_lx'] = sprintf("%.2f", $bj + ($bj * $val['hoperate1'] / 36500 * $days));
				}
			}
			/** 新手专享 */
			if (strstr($val['productname'], "新手专享")) {
				$val['is_new'] = 1;
			} else {
				$val['is_new'] = 0;
			}
			/** APP专享 */
			if (strstr($val['productname'], "APP专享")) {
				$val['is_app'] = 1;
			} else {
				$val['is_app'] = 0;
			}
			/** 使用代金券 */
			if ($val['isdjj'] == 1) {
				$val['is_daijin'] = 1;
			} else {
				$val['is_daijin'] = 0;
			}
			/** 收益增值券 */
			if ($coupon_model->check_jiaxijuan_product($val['id'])) {
				$val['is_jiaxijuan'] = 1;
			} else {
				$val['is_jiaxijuan'] = 0;
			}
			/** 518预热活动 加息活动 */
			$val['jx'] = $this->_activty_jiaxi_518($val['id']);

		} /** end foreach */

		/** TODO 数据返回 */
		$data = array(
			'page' => $page,
			'total' => $total_list,
			'data' => $plist
		);
		return $this->_return($data);
	}/*}}}*/

	/**
	 * 产品详细信息 
	 * 
	 * METHOD：GET
	 *
	 * @access public
	 * @return void
	 */
	public function detail()/*{{{*/
	{
		$id = I('get.id') + 0; /** 产品id */
		$products_model = new ProductsModel();
		$coupon_model = new CouponModel();

		/** 获取产品信息 */
		$detail = $products_model->product_detail($id);

		/** 获取产品的年利率 */
		$yield = $products_model->get_productsyield($id);
		//$detail['yield'] = $yield; // 年收益率

		/** 获取产品的风控措施 */
		$fk = $products_model->get_productsfk($id);
		//$detail['fk'] = $fk;

		/** 获取产品的内容 */
		$content = $products_model->sel_product_all_content($id);
		/** 内容处理 */
		if(!empty($content['content'])) {
			$content['content'] = str_replace('/upload/news_images/ueditor/php/upload/','https://images.88.com.cn/upload/news_images/ueditor/php/upload/',$content['content']);
		}
		if(!empty($content['description'])) {
			$content['description'] = str_replace('/upload/news_images/ueditor/php/upload/','https://images.88.com.cn/upload/news_images/ueditor/php/upload/',$content['description']);
		}
		if(!empty($content['riskcontrol'])) {
			$content['riskcontrol'] = str_replace('/upload/news_images/ueditor/php/upload/','https://images.88.com.cn/upload/news_images/ueditor/php/upload/',$content['riskcontrol']);
		}
		//$detail['content'] = $content;

		/** 处理复利宝的trouble */
		if ($detail['ptype'] > 20) {
			$flbtype = $products_model->info_flbtype($detail['ptype']);
			$detail['productname'] = mb_substr($flbtype['flbtype'], 0, 3) . '&nbsp;' . $detail['productname'];
		}
		/** 判断定投产品转让 */
		if ($detail['istransfer'] && $detail['ptype']==1) { // 可以转让
			if (empty($detail['transfer_difftime'])) {
				$detail['transfer_difftime'] = '{"6":15,"9":20,"12":30,"18":45,"24":60,"36":90}';
			}
			$tzzrarr = @json_decode($detail['transfer_difftime'], 1); // 投资转让 转换数组
			if ($detail['investtime'] >= 6) {
				$tzday = $tzzrarr[$detail['investtime']]; // 投资多少天转让
				$syts = $tzday; // 提示多少天转让
			}
			if ($detail['investtime1'] >= 6) {
				$tzday = $tzzrarr[$detail['investtime1']]; // 投资多少天转让
				$syts .= ($syts ? ',' : '').$tzday;
			}
			if ($detail['investtime2'] >= 6) {
				$tzday = $tzzrarr[$detail['investtime2']]; // 投资多少天转让
				$syts .= ($syts ? ',' : '').$tzday;
			}
			$detail['syts'] = $syts;
		} else {
			$detail['syts'] = '';
		}
		/** 投资进度 */
		if ($detail['totalamount'] > 0) {
			$detail['bl'] = sprintf("%.2f", $detail['totalmoney'] / $detail['totalamount'] * 100);
		} else {
			$detail['bl'] = 0;
		}

		/** 是否能使用88币的产品*/
		$detail['cancoins'] = $this->_can_use_coin88($id);
		/** 体验标 */
		if ($detail['exp_ptype'] == 2) {
			/**TODO 暂无处理*/
		}
		/** 518预热活动 加息活动 */
		$detail['jx'] = $this->_activty_jiaxi_518($id);
		$detail['yield'] = $yield; // 年收益率
		$detail['fk'] = $fk; /** 风控措施*/
		$detail['content'] = $content; // 产品内容

		$data = array(
			'data' => $detail
		);
		
		return $this->_return($data);
	}/*}}}*/

	/**
	 * 投资记录 
	 * 
	 * METHOD：GET
	 *
	 * @param id 产品id
	 * @access public
	 * @return void
	 */
	public function history()/*{{{*/
	{
		$id = I('get.id') + 0; /** 产品id */
		$page = I('get.page', 1);
		$page = ($page <= 0) ? 1 : $page;
		$pagesize = 10;

		$products_model = new ProductsModel();
		/** 查询条件 */
		$where = "data_orders.pdid='{$id}' and data_orders.payStatus=2 ";
		$total = $products_model->get_countofproduct_history($where);
		$list = array();
		if (($page * $pagesize) <= ($total+$pagesize)) {
			$list = $products_model->get_productofhistory($where, $page, $pagesize);
		}
		/** 数据处理 */
		foreach ($list as $key=>$value) {
			$list[$key]['index'] = $key + 1 + ($page - 1) * $pagesize; /** 序号 */
			$list[$key]['accountmoney'] = number_format($value['accountmoney'], 2);
			$list[$key]['paytime'] = date("Y-m-d H:i", $value['paytime']);
			$list[$key]['realname'] = cut_string($value['realname']) . '**';
		}
		/** 返回数据 */
		$data = array(
			'page' => $page,
			'total' => $total,
			'data' => $list
		);
		return $this->_return($data);
	}/*}}}*/

	/**
	 * 518 加息活动 
	 * 
	 * @param int $id 产品id 
	 * @access private
	 * @return int 返回加息利率
	 */
	private function _activty_jiaxi_518($id)/*{{{*/
	{
		$jx_array = C('jx_array');
		$jx_date = C('jx_date');
		if (in_array($id, $jx_array['id'])) {
			/** 判断是否是在活动期内 */
			$check_result = check_product_jx($jx_array, $jx_date);
			if ($check_result) {
				/** 加息利率 */
				$jx = $jx_array['jx'][$val['id']];
			} else {
				$jx = 0;
			}
		} else {
			$jx = 0;
		}
		
		return $jx;	
	}/*}}}*/

	/**
	 * 判断产品是否能使用88币 
	 * 
	 * @param int $id 产品id 
	 * @access private
	 * @return int
	 */
	private function _can_use_coin88($id)/*{{{*/
	{
		$rules_model = new RulesModel();
		$can_usecoins_88 = $rules_model->canUserCoinsProductsIds();
		$can_usecoins_88 = array2a1($can_usecoins_88);
		/** 是否能使用88币*/
		if (in_array($id, $can_usecoins_88)) {
			$cancoins = 1;
		} else {
			$cancoins = 0;
		}
		
		return $cancoins;
	}/*}}}*/

}

/* end file */
