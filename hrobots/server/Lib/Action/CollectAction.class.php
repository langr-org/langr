<?php
/**
 * @file CollectAction.class.php
 * @brief 
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package Action
 * @author Langr <hua@langr.org> 2014/05/04 11:43
 * 
 * $Id: CollectAction.class.php 116 2014-07-09 09:52:34Z huanghua $
 */

class CollectAction extends Action 
{
	function __construct() /* {{{ */
	{
		parent::__construct();
		@include(CONF_PATH.'/api_config.php');
		$this->config = $api_config;
	} /* }}} */

	public function index() /* {{{ */
	{
		header("Content-Type:text/html; charset=utf-8");
		echo "Hello,I'm Robots!";
		return ;
	} /* }}} */

	public function _empty() /* {{{ */
	{
		echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		return ;
	} /* }}} */

	/**
	 * @fn
	 * @brief 采集分类
	 * 	NOTE: 一般在 CLI 模式运行
	 * 	从 $api_config['category_index'] 取 总分类入口，再分析子分类链接
	 * @param mod 模块
	 * @param level 分类级别
	 * @return 
	 */
	public function category() /* {{{ */
	{
		$mod = '';
		$level = 1;		/* 分类级别，默认从第一级分类开始采集 */
		if ( !empty($_GET['mod']) ) {
			$mod = $_GET['mod'];
		} else {
			$api = A('Api');
			$mod = $api->priority();
		}
		if ( !empty($_GET['level']) ) {
			$level = $_GET['level'];
		}

		$mod_file = CONF_PATH.'/'.$mod.'.module.php';
		if ( !is_file($mod_file) ) {
			wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', 'Not installed module: '.$mod, true);
			return ;
		}
		include_once($mod_file);

		$m = M($mod.'_ctg_url'); 
		//$m = D('CtgUrl');
		//$m = new CtgUrlModel($mod.'_ctg_url');
		if ( $level == 1 ) {
			$result = $m->where("src='$mod' and ctg_level=0 and status=1")->field('ctg_url,ctg_level')->select();
			if ( !$result ) {
				$mod_config['category_index']['src'] = $mod;
				$mod_config['category_index']['status'] = 1;
				$mod_config['category_index']['created'] = time();
				/* 覆盖，防止状态不正确的旧数据？ */
				$m->add($mod_config['category_index'], null, true);
			}
		}

		wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', "$mod category start to collect...", true);
		$t_level = count($mod_config['category_rules']);
		for ( $l = $level; $l <= $t_level; $l++ ) {
			wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', "$mod total level:$t_level, current level:$l", true);
			$this->_category($m, $mod, $l, $mod_config);
			wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', "$mod level:$l ok!", true);
		}
		wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', "$mod category collect completed.", true);

		return ;
	} /* }}} */

	/**
	 * @fn
	 * @brief 递归采集指定模块分类
	 * @param module 模块
	 * @param level 分类级别
	 * @param mod_config 模块配置
	 * @param rules 每级的匹配规则
	 * @return 
	 */
	public function _category($model, $module, $level = 1, $mod_config = array()) /* {{{ */
	{
		$p_level = $level - 1;
		$t_level = count($mod_config['category_rules']);
		/* 采集子分类无limit限制, 指定时间内更新过的记录不再更新 */
		$where = "src='$module' and ctg_level='$p_level' and status>0 and updated<".(time()-$this->config['priority']['time']);
		$result = $model->where($where)->field('id,ctg_url,ctg_level,status')->select();
		/* 没有结果，表示此级任务完成 */
		if ( !$result ) {
			return null;
		}

		wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', 'get tasks:'.count($result).', trying to collect...', true);
		/* 广度优先搜索, 找出各当前级别分类的子分类 */
		$__c = 0;
		foreach ( $result as $r ) {
			$html = $this->read_url($r['ctg_url'], null, $mod_config['header'], $this->config['priority']['retry']);
			if ( strlen($html) < 100 ) {		/* 采集失败，跳过 */
				continue;
			}
			$now = time();
			/* 指定层级分类匹配分析：每页面的规则数 */
			foreach ( $mod_config['category_rules'][$level] as $rk => $rv ) {
				/* 分析, 指定层级关键规则没有匹配则表示当前分类是最后级(同时是产品列表) */
				//preg_match($rv['rule'], $html, $res);
				if ( preg_match_all($rv['rule'], $html, $res) == 0 ) {
					if ( $rk == 'key' ) {
						$r['status'] = 0;
					}
					continue;
				}
				/* ... $rk 特别规则可在此处理 */

				/* 每条规则匹配到的记录条数 */
				$_ret = array();
				$count = count($res[0]);
				$_c_ = count($rv);
				for ( $j = 0; $j < $count; $j++ ) {
					$_ret[$j]['ctg_level'] = $r['ctg_level'] + 1;
					$_ret[$j]['src'] = $module;
					$_ret[$j]['referer'] = $r['ctg_url'];
					$_ret[$j]['pid'] = $r['id'];
					$_ret[$j]['status'] = 1;
					$_ret[$j]['created'] = $now;
					//$_ret[$j]['updated'] = $now;
					/* 配置的最后一级 */
					if ( $level == $t_level ) {
						$_ret[$j]['status'] = 0;
					}
					/* 每条记录的匹配词数 */
					for ( $i = 1; $i < $_c_; $i++ ) {
						if ( $count == 1 ) {
							$_ret[$j][$rv[$i]] = trim(strip_tags($res[$i]));
							continue;
						}
						$_ret[$j][$rv[$i]] = trim(strip_tags($res[$i][$j]));
					}
					if ( !empty($_ret[$j]['gds_sum']) ) {
						$_ret[$j]['gds_sum'] = str_replace(',', '', $_ret[$j]['gds_sum']);
					}
					if ( !empty($_ret[$j]['ctg_url']) && substr($_ret[$j]['ctg_url'], 0, 4) != 'http' ) {
						$_ret[$j]['ctg_url'] = href_url($r['ctg_url'], $_ret[$j]['ctg_url']);
					}
					//wlog('newark.ctg_url.txt', ",\"".$_ret[$j]['ctg_name']."\",\"".$_ret[$j]['gds_sum']."\",\"".$_ret[$j]['ctg_url']);
				}
				$flag = $model->addAll($_ret, null, true);	/* 覆盖旧数据？ */
				if ( !$flag ) {
					wlog($module.date('Y-m').'.txt', 'insert error.'.print_r($model, true));
				}
				wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', 'collect data:'.$count);
			}
			/* 一个页面抓取分析完毕 */
			$r['updated'] = $now;
			$model->where('id='.$r['id'])->save($r);
			$__c++;
		} /* end foreach result */
		wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', 'completed tasks:'.$__c, true);

		return ;
	} /* }}} */

	/**
	 * @fn
	 * @brief 采集产品列表
	 * 	NOTE: 一般在 CLI 模式运行
	 * 	从 ctg_url 取 status=0 的最后级分类，再分析分页链接
	 * @param mod 模块
	 * @return 
	 */
	public function goodslist() /* {{{ */
	{
		$mod = '';
		$level = 1;		/* 分类级别，默认从第一级分类开始采集 */
		if ( !empty($_GET['mod']) ) {
			$mod = $_GET['mod'];
		} else {
			$api = A('Api');
			$mod = $api->priority();
		}

		$mod_file = CONF_PATH.'/'.$mod.'.module.php';
		if ( !is_file($mod_file) ) {
			wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', 'Not installed module: '.$mod, true);
			return ;
		}
		include_once($mod_file);

		wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', "$mod goodslist start to collect...", true);
		$m = M($mod.'_ctg_url');
		$g = M($mod.'_goodslist_url');
		//$m = D('CtgUrl');
		//$m = new CtgUrlModel($mod.'_ctg_url');
		$__t = 0;		/* 处理的任务总数 */
		$__c = 0;		/* 实际采集数 */
		$__p = 0;		/* 生成的列表页数 */
		$where = "src='$mod' and status=0";
		//$result = $m->where($where)->field('id,ctg_url,gds_sum,gds_pages')->limit($this->config['priority']['task'])->select();
		$result = $m->where($where)->field('id,pid,ctg_name,ctg_url,gds_sum,gds_pages')->select();
		/* 没有结果表示任务完成 */
		if ( !$result ) {
			wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', $mod.' goodslist null.', true);
			return 'done';
		}

		wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', 'get tasks:'.count($result).', trying to collect...', true);
		foreach ( $result as $r ) {
			$now = time();
			/* gds_sum,gds_pages 其中有一个有数据就不需要再采集 */
			if ( $r['gds_sum'] == 0 && $r['gds_pages'] == 0 ) {
				$html = $this->read_url($r['ctg_url'], null, $mod_config['header'], $this->config['priority']['retry']);
				if ( strlen($html) < 100 ) {			/* 采集失败，跳过 */
					continue;
				}
			
				foreach ( $mod_config['goodslist_rules'] as $rk => $rv ) {
					if ( $rk == 'gdslist' ) {
						continue;
					}
					/* 每个规则 只需匹配一条记录，一个词 */
					preg_match($rv['rule'], $html, $res);
					$s[$rv[1]] = $r[$rv[1]];
					$r[$rv[1]] = empty($res[1]) ? $r[$rv[1]] : $res[1];
					if ( $rk == 'goods_sum' && !empty($res[1]) ) {
						$r['gds_sum'] = str_replace(',', '', $r['gds_sum']);
					}
				}
				$r['updated'] = $now;
				$m->where('id='.$r['id'])->save($r);
				$__c++;
				wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', 'update cid:'.$r['id'].' gds_sum:'.$s['gds_sum'].'-'.$r['gds_sum'].' gds_pages:'.$s['gds_sum'].'-'.$r['gds_pages']);
			}

			$_ret = array();
			$fn_page = $mod.'_goods_pages';
			if ( function_exists($fn_page) ) {
				$_ret = $fn_page($r['ctg_url'], $r['gds_sum'], $r['gds_pages']);
			}
			$count = count($_ret);
			for ( $j = 0; $j < $count; $j++ ) {
				$_ret[$j]['ctg_id'] = $_ret[$j]['cid'] = $r['id'];
				$_ret[$j]['ctg_name'] = $r['ctg_name'];
				$_ret[$j]['ctg_pid'] = $r['pid'];
				$_ret[$j]['src'] = $mod;
				$_ret[$j]['referer'] = $r['ctg_url'];
				$_ret[$j]['created'] = $now;
			}
			$flag = $g->addAll($_ret, null, true);	/* 覆盖旧数据？ */
			if ( !$flag ) {
				wlog(MODULE_NAME.'-'.ACTION_NAME.date('Y-m-d').'.txt', 'insert error.'.$g->getDbError());
				wlog(MODULE_NAME.'-'.ACTION_NAME.date('Y-m-d').'.txt', print_r($_ret, true));
			}
			wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', 'gds_sum:'.$r['gds_sum'].'get pages:'.$count.',url:'.$r['ctg_url']);
			$__t++;
			$__p += $count;
		}
		wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', 'completed tasks:'.$__t.',collect:'.$__c.',get pages:'.$__p, true);
		wlog(MODULE_NAME.'-'.ACTION_NAME.'.log', $mod.' goodslist collect completed.', true);
		return 'done';
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
