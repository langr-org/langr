<?php
/**
 * @file PushAction.class.php
 * @brief 
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package Action
 * @author Langr <hua@langr.org> 2014/05/28 14:58
 * 
 * $Id: PushAction.class.php 116 2014-07-09 09:52:34Z huanghua $
 */

class PushAction extends Action 
{
	function __construct() /* {{{ */
	{
		parent::__construct();
		@include(CONF_PATH.'/api_config.php');
		$this->config = $api_config;
		$this->config['big_pwd'] = "ocs~rsh,uBZN";
		$this->config['big_session_key'] = '';
		$this->config['big_register'] = $api_config['big_host']."/Security/RegisterUser/json/T0030";
		$this->config['big_modify'] = $api_config['big_host']."/ModifyData/OitsPrice/json/";
		$this->config['big_update'] = $api_config['big_host']."/Update/json/";
		$this->_register();
		wlog('push.'.date('Y-m-d').'.log', $this->config['big_session_key'], true);
	} /* }}} */

	/**
	 * @brief bigdata
	 * 	Update
	 */
	public function index() /* {{{ */
	{
		/* 推送 */
		$mod = '';
		$order = '';
		if ( !empty($_GET['mod']) ) {
			$mod = $_GET['mod'];
		} else {
			$api = A('Api');
			$mod = $api->priority();
		}
		if ( !empty($_GET['order']) ) {
			$order = 'desc';
		}

		$m = M($mod."_goods_url");
		$m2 = M($mod.'_goods');
		$limit = 100;
		wlog('push.'.date('Y-m-d').'.log', $mod.' push bigdata starting...', true);
		while ( true ) {
			if ( date('Hi') > '0900' && date('Hi') < '1800' ) {
				if ( date('Hi') == '0900' || date('Hi') == '1800' ) {
					wlog('push.'.date('Y-m-d').'.log', 'working time, stop push!', true);
				}
				sleep(1);
			//	break;
			}
			$now = time();
			$_ret = array();
			$this->_register();
			$result = $m->query("select gu.id,g.gds_name,g.gds_sn,g.src,g.provider,g.inventory,g.warehouse,g.prices,g.gds_description,g.encap,g.package,g.rohsstate,g.status from cot_{$mod}_goods_url gu,cot_{$mod}_goods g where gu.gds_id=0 and gu.status=2 and g.gid=gu.id order by gu.id $order limit $limit");
			$c = 0;
			$where = 'in ( ';
			foreach ( $result as $r ) {
				$where .= $r['id'].',';
				$prices = json_decode($r['prices'], true);
				$l = 0;
				foreach ( $prices as $k => $p ) {
					if ( empty($p) || $p <= 0 ) {
						continue;
					}
					//$_tmp_price = $p/$k;	/* 暂不计算 */
					$_ret[$c]['Items'][$l]['LineNum'] = $l + 1;
					$_ret[$c]['Items'][$l]['Quantity'] = str_replace(',', '', $k);
					$_ret[$c]['Items'][$l]['Price'] = str_replace(',', '', $p);
					$l++;
				}
				$_ret[$c]['CardName'] = ucfirst($r['src']);
				$_ret[$c]['BrandName'] = $r['provider'];
				$_ret[$c]['ItemDesc'] = addslashes(substr($r['gds_description'], 0, 98));
				$_ret[$c]['Quantity'] = (int) (str_replace(',', '', $r['inventory']));
				$_ret[$c]['HQID'] = $r['id'];
				$_ret[$c]['Encap'] = '-';
				$_ret[$c]['Package'] = '-';
				$_ret[$c]['Warehouse'] = '-';
				$_ret[$c]['ShowPrice'] = 'Y';
				$_ret[$c]['PriceParity'] = 'Y';
				$_ret[$c]['MOQ'] = '1';
				$_ret[$c]['Rohs'] = 'Y';
				$_ret[$c]['ModelCode'] = str_replace("'", ".", ($r['gds_name']));
				$_ret[$c]['SuppliersItemCode'] = str_replace("'", ".", $r['gds_sn']);
				/* NOTE: 新采集的分类没有对应，所以不要送 */
				//$_ret[$c]['SupplierCategory'] = $r['ctg_id'];
				$c++;
			}
			if ( $c == 0 ) {
				wlog('push.'.date('Y-m-d').'.log', 'push done!', true);
				/* 在后台，程序可能会被重启，所以休息一下 */
				sleep(10);
				break;
			}
			$where = substr($where, 0, -1).')';
			/* */
			$api = $this->config['big_update'].$this->config['big_session_key'];
			for ( $i = 1; $i <= $this->config['priority']['retry']; $i++ ) {
				$ret = http2host($api, json_encode($_ret));
				if ( $ret[0] != '-' ) {
					break;
				}
			}
			if ( $ret[0] == '-' ) {
				wlog('push.'.date('Y-m-d').'.log', 'push bigdata connection error:'.$ret);
				return false;
			}
			wlog('push.'.date('Y-m-d-H').'.log', 'push bigdata:'.$ret);
			$ret = json_decode($ret, true);
	
			$m->where('id '.$where)->save(array('status'=>'4','updated'=>$now));
			$m2->where('gid '.$where)->save(array('status'=>'4','updated'=>$now));
			if ( $ret['ErrorCode'] == '0' ) {
				//wlog('push.'.date('Y-m-d-H').'.log', 'push data:'.json_encode($_ret));
				foreach ( $ret['UpdateItems'] as $tmp ) {
					$m->where('id='.$tmp['HQID'])->save(array('gds_id'=>$tmp['ID']));
				}
			} else {
				wlog('push.'.date('Y-m-d-H').'.log', 'push data:'.json_encode($_ret));
				wlog('push.'.date('Y-m-d').'.log', 'push bigdata error:'.$ret['ErrorCode'].$ret['ErrorMessage'], true);
			}
		}
		return ;
	} /* }}} */

	/**
	 * @brief bigdata
	 * 	ModifyData
	 */
	public function prices() /* {{{ */
	{
		/* 推送 */
		$mod = '';
		$order = '';
		if ( !empty($_GET['mod']) ) {
			$mod = $_GET['mod'];
		} else {
			$api = A('Api');
			$mod = $api->priority();
		}
		if ( !empty($_GET['order']) ) {
			$order = 'desc';
		}

		$m = M($mod."_goods_url");
		$m2 = M($mod.'_goods');
		$limit = 100;
		wlog('push.prices.'.date('Y-m-d').'.log', $mod.' push prices starting...', true);
		while ( true ) {
			if ( date('Hi') > '0900' && date('Hi') < '1800' ) {
				if ( date('Hi') == '0900' || date('Hi') == '1800' ) {
					wlog('push.prices.'.date('Y-m-d').'.log', 'working time, stop push!', true);
				}
				sleep(1);
			//	break;
			}
			$now = time();
			$_ret = array();
			$this->_register();
			/* 去掉 order by gu.power desc, 有点慢 */
			$result = $m->query("select gu.id,gu.gds_id,g.gds_name,g.gds_sn,g.src,g.provider,g.inventory,g.warehouse,g.prices,g.gds_description,g.encap,g.package,g.rohsstate,g.status from cot_{$mod}_goods_url gu,cot_{$mod}_goods g where gu.gds_id>0 and g.gid=gu.id and gu.status=2 order by gu.id $order limit $limit");
			$c = 0;
			$where = 'in ( ';
			foreach ( $result as $r ) {
				$where .= $r['id'].',';
				$prices = json_decode($r['prices'], true);
				$l = 0;
				$_ret[$c]['ID'] = $r['gds_id'];
				$_ret[$c]['OnHand'] = (int) (str_replace(',', '', $r['inventory']));
				foreach ( $prices as $k => $p ) {
					if ( empty($p) || $p <= 0 ) {
						continue;
					}
					$_ret[$c]['Items'][$l]['Quantity'] = str_replace(',', '', $k);
					$_ret[$c]['Items'][$l]['Price'] = str_replace(',', '', $p);
					$l++;
				}
				$c++;
			}
			if ( $c == 0 ) {
				wlog('push.prices.'.date('Y-m-d').'.log', 'push done!', true);
				/* 在后台，程序可能会被重启，所以休息一下 */
				sleep(10);
				break;
			}
			$where = substr($where, 0, -1).')';
			/* */
			$api = $this->config['big_modify'].$this->config['big_session_key'].'/N';
			for ( $i = 1; $i <= $this->config['priority']['retry']; $i++ ) {
				$ret = http2host($api, json_encode($_ret));
				if ( $ret[0] != '-' ) {
					break;
				}
			}
			if ( $ret[0] == '-' ) {
				wlog('push.prices.'.date('Y-m-d').'.log', 'push prices connection error:'.$ret);
				return false;
			}
			wlog('push.prices.'.date('Y-m-d-H').'.log', 'push bigdata:'.$ret);
			$ret = json_decode($ret, true);
	
			$m->where('id '.$where)->save(array('status'=>'4','updated'=>$now));
			$m2->where('gid '.$where)->save(array('status'=>'4','updated'=>$now));
			if ( $ret['ErrorCode'] == '0' ) {
				//wlog('push.prices.'.date('Y-m-d-H').'.log', 'push data:'.json_encode($_ret));
			} else {
				wlog('push.prices.'.date('Y-m-d-H').'.log', 'push data:'.json_encode($_ret));
				wlog('push.prices.'.date('Y-m-d').'.log', 'push bigdata error:'.$ret['ErrorCode'].$ret['ErrorMessage'], true);
			}
		}
		return ;
	} /* }}} */

	/**
	 * @fn
	 * @brief bigdata
	 * @param password
	 * @return 
	 */
	public function _register() /* {{{ */
	{
		$api = $this->config['big_register'];
		for ( $i = 1; $i <= $this->config['priority']['retry']; $i++ ) {
			$ret = http2host($api, md5($this->config['big_pwd']));
			if ( $ret[0] != '-' ) {
				break;
			}
		}
		if ( $ret[0] == '-' ) {
			wlog('push.'.date('Y-m-d').'.log', 'get bigdata session error:'.$ret);
			return false;
		}
		$ret = json_decode($ret, true);
		$this->config['big_session_key'] = $ret['Result'];
		return true;
	} /* }}} */

	public function _empty() /* {{{ */
	{
		echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		return ;
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param 	
	 * @return 
	 */
	protected function read_url($url, $post = null, $header = null, $retry = 5) /* {{{ */
	{
		$html = '';
		/* 连接出错时，最多试n次后跳过此数据 */
		for ( $i = 1; $i <= $retry; $i++ ) {
			$html = http2host($url, $post, $header);
			if ( $html[0] == '-' && strlen($html) < 100 ) {
				wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', $retry.','.$i.' Connection error: '.$html.'('.$url.')', true);
			} else if ( strlen($html) < 500 ) {
				wlog(MODULE_NAME.'-'.ACTION_NAME.date('Y-m-d').'.txt', '('.$url.')'.$html);
				wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', $retry.','.$i.' Collection might be wrong, please tell us.('.$url.')', true);
			} else {
				break;
			}
		}
		return $html;
	} /* }}} */
}
/* end file */
