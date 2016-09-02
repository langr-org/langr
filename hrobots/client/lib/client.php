<?php
/* D:\wamp\bin\php\php5.3.13\php -f */
/**
 * @file client.php
 * @brief client 采集基类
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package client
 * @author Langr <hua@langr.org> 2014/04/28 16:04
 * 
 * $Id: client.php 112 2014-06-27 07:35:26Z huanghua $
 */

class client
{
	protected $config = array();
	protected $client = array();		/* client info */
	protected $device = array();		/* device info */
	//protected $task = array();		/* task info */
	//protected $data = array();		/* collect data */
	/**
	 * @WARNNING!!! copy apiction.class.php.
	 */
	/* {{{ */
	const E_OK = 0;
	const E_ARGS = 100;
	const E_LOGOFF = 101;
	const E_DATA_INVALID = 103;
	const E_NOOP = 104;
	const E_IP_DENY = 105;
	const E_DATA_EMPTY = 110;
	const E_OP_FAIL = 111;
	const E_CHECKSUM = 150;
	const E_VERSION = 151;
	const E_API_NO_EXIST = 500;
	const E_KEY_NO_EXIST = 501;
	const E_SYS_1 = 503;
	const E_SYS = 505;
	const E_UNKNOW = 999;
	/* }}} */

	function __construct() /* {{{ */
	{
		include(APP_PATH.'conf/cli_config.php');
		$this->config = $api_config;
		$this->getClient();
		if ( function_exists('sys_windows') ) {
			$this->device = sys_windows();
		}
		$this->device['PHP_OS'] = PHP_OS;
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @return  
	 */
	public function index() /* {{{ */
	{
		return ;
	} /* }}} */

	/**
	 * @fn
	 * @brief get task
	 * @access action
	 * @return task array. 
	 */
	public function gettask($module = '') /* {{{ */
	{
		$args['module'] = empty($_GET['module']) ? $module : $_GET['module'];
		$args['client'] = $this->client;
		$args['client']['device'] = $this->device;
		$arg = $this->doargs('gettask', $args);

		$ret = http2host($this->config['server'], $arg);
		if ( empty($ret) || $ret[0] == '-' ) {
			wlog('client.'.date('Y-m-d').'.log', '-10 服务器连接出错：'.$ret.'('.$this->config['server'].')', true);
			return false;
		}
		$_ret = json_decode($ret, true);
		if ( $_ret == false || $_ret == null || empty($_ret['ret']['do']) ) {
			wlog('client.'.date('Y-m-d').'.log', '-11 服务端返回数据异常。'.'('.$this->config['server'].')', true);
			wlog('client.'.date('Y-m-d').'.log', $ret);
		}
		if ( $_ret['errno'] != self::E_OK ) {
			if ( substr(PHP_OS, 0, 3) == 'WIN' ) {
				$_ret['errmsg'] = iconv('UTF-8', 'GBK', $_ret['errmsg']);
			}
			wlog('client.'.date('Y-m-d').'.log', $_ret['errno'].$_ret['errmsg'], true);
			if ( $_ret['errno'] == self::E_VERSION || $_ret['errno'] == self::E_CHECKSUM ) {
				exit;
			}
			return false;
		}
		$this->device = '';		/* once */
		return $_ret['ret'];
	} /* }}} */

	/**
	 * @fn
	 * @brief put task
	 * @access action
	 * @return true or false. 
	 */
	public function puttask(& $task = array()) /* {{{ */
	{
		if ( $task == false || !count($task) || empty($task['do']) ) {
			return false;
		}
		$task['client'] = $this->client;
		$count = count($task['list']);
		wlog('client.'.date('Y-m-d').'.log', '完成'.$task['module'].'('.$task['taskid'].')任务:'.$count.'条, 正准备提交数据['.$task['do'].']...', true);
		$post = json_encode(array('api'=>'puttask', 'args'=>$task));
		$ret = '';
		for ( $i = 1; $i <= $this->config['priority']['retry']; $i++ ) {
			$ret = http2host($this->config['server'], $post);
			if ( $ret[0] != '-' ) {
				$_ret = json_decode($ret, true);
				if ( $_ret['errno'] == self::E_OK ) {
					break;
				}
			}
			/* 不抛弃，不放弃 */
			wlog('client.'.date('Y-m-d').'.log', '-30 提交数据出错，再次提交...'.$i, true);
			if ( $i == $this->config['priority']['retry'] ) {
				wlog('client.'.date('Y-m-d').'.log', '-31 提交数据出错，放弃数据:'.$ret);
				file_put_contents(APP_PATH.'logs/cache/'.date('Ymd-').$task['taskid'].'.txt', $post);
				return false;
			}
		}

		wlog('client.'.date('Y-m-d').'.log', '提交'.$task['module'].'有效数据:'.$_ret['ret']['count'], true);
		return true;
	} /* }}} */

	/**
	 * @fn
	 * @brief 开始采集工作
	 * @param 
	 * @return task array.
	 */
	public function dotask(& $task) /* {{{ */
	{
		if ( $task == null || $task == false ) {
			return false;
		}
		if ( empty($task['list']) || $task['count'] == 0 ) {
			wlog('client.'.date('Y-m-d').'.log', $task['module'].' 无采集任务。', true);
			return false;
		}
		$count = count($task['list']);
		$do = $task['do'];
		$mod = $task['module'];
		$mod_file = APP_PATH.'module/'.$mod.'.module.php';
		if ( !is_file($mod_file) ) {
			wlog('client.'.date('Y-m-d').'.log', '-41 未安装模块：'.$mod, true);
			return false;
		}
		include($mod_file);

		wlog('client.'.date('Y-m-d').'.log', '获得'.$mod.'('.$task['taskid'].')任务:'.$count.'条, 开始拼命采集中['.$task['do'].']...', true);
		$task = $this->$do($task, $mod_config);
		return $task;
	} /* }}} */

	/**
	 * @fn
	 * @brief collect task
	 * @access action
	 * @return  
	 */
	public function collect(& $task, $mod_config) /* {{{ */
	{
		$__c = 0;				/* 实际采集数 */
		$__p = 0;				/* 实际采集产品数 */
		$ret = array();
		foreach ( $task['list'] as $r ) {
			$html = $this->read_url($r['url'], null, $mod_config['header'], $this->config['priority']['retry']);
			if ( strlen($html) < 100 ) {	/* 采集失败，跳过 */
				continue;
			}
			$rk = 'gdslist';
			$rv = $mod_config['goodslist_rules']['gdslist'];
			$__w = count($rv);		/* 规则的匹配词数 */
			/* 产品列表，匹配全部记录，没有则跳过 */
			$p_flag = preg_match_all($rv['rule'], $html, $res);
			if ( $p_flag === 0 || $p_flag === false ) {
				wlog('client.'.date('Y-m-d Hi').'.log.html', $r['url'].$html);
				continue;
			}

			/* 规则匹配到的记录条数 */
			$_ret = array();
			$count = count($res[0]);
			$__p += $count;
			for ( $j = 0; $j < $count; $j++ ) {
				/* 每条记录的匹配词数 */
				for ( $i = 1; $i < $__w; $i++ ) {
					$_ret[$j][$rv[$i]] = trim(strip_tags($res[$i][$j]));
					/* TODO: 二级匹配 */
				}
				$_ret[$j]['lid'] = $r['id'];
				$_ret[$j]['referer'] = $r['url'];
				$_ret[$j]['gds_url'] = href_url($r['url'], $_ret[$j]['gds_url']);
				if ( isset($_ret[$j]['provider_url']) ) {
					$_ret[$j]['provider_url'] = href_url($r['url'], $_ret[$j]['provider_url']);
				}
			}
			$__c++;
			$ret[] = $_ret;
		}
		wlog('client.'.date('Y-m-d').'.log', 'collect pages:'.$__c.', get goods:'.$__p);
		wlog('client.'.$task['module'].'.'.$task['do'].date('Y-m-d').'.txt', json_encode($ret));
		$task['list'] = $ret;
		return $task;
	} /* }}} */

	/**
	 * @fn
	 * @brief update task
	 * @access action
	 * @return  
	 */
	public function update(& $task, $mod_config) /* {{{ */
	{
		$__c = 0;				/* 实际采集数 */
		$_ret = array();			/* 结果 */
		foreach ( $task['list'] as $r ) {
			$html = $this->read_url($r['url'], null, $mod_config['header'], $this->config['priority']['retry']);
			if ( strlen($html) < 100 ) {	/* 采集失败，跳过 */
				continue;
			}
			$_ret[$__c] = $this->g_rules($mod_config['goods_rules'], $html);

			if ( isset($_ret[$__c]['gds_doc']) ) {
				$_ret[$__c]['gds_doc'] = href_url($r['url'], $_ret[$__c]['gds_doc']);
			}
			if ( isset($_ret[$__c]['gds_thumb']) ) {
				$_ret[$__c]['gds_thumb'] = href_url($r['url'], $_ret[$__c]['gds_thumb']);
			}
			if ( isset($_ret[$__c]['gds_img']) ) {
				$_ret[$__c]['gds_img'] = href_url($r['url'], $_ret[$__c]['gds_img']);
			}
			$_ret[$__c]['id'] = $r['id'];
			$__c++;
		}
		wlog('client.'.date('Y-m-d').'.log', 'update goods:'.$__c);
		//wlog('client.'.$task['module'].'.'.$task['do'].date('Y-m-d').'.txt', json_encode($_ret));
		$task['list'] = $_ret;
		return $task;
	} /* }}} */

	/**
	 * @fn
	 * @brief 无限通用匹配规则
	 * @param 
	 * @return 
	 * @see conf/mouser.module.php $mod_config['goods_rules']
	 */
	protected function g_rules($rules, & $html) /* {{{ */
	{
		$_ret = array();			/* 结果 */
		$res = array();
		foreach ( $rules as $rk => $rv ) {
			$__w = count($rv);		/* 规则的匹配词数 */
			/* 匹配全部记录，没有则跳过 */
			$p_flag = preg_match_all($rv['rule']['match'], $html, $res);
			if ( $p_flag === 0 || $p_flag === false ) {
				continue;
			}

			/* 规则匹配到的记录条数 */
			$count = count($res[0]);
			$real_count = $count;
			if ( $rv['rule']['list'] != 'all' ) {
				$count = $rv['rule']['list'];
			}
			for ( $j = 0; $j < $count; $j++ ) {
				/* 每条记录的匹配词数 */
				for ( $i = 1; $i < $__w; $i++ ) {
					/* 子级匹配 */
					if ( is_array($rv[$i]) ) {
						if ( empty($rv['rule']['key']) ) {
							$_ret += $this->g_rules($rv[$i], $res[$i][$j]);
						} else {
							$_ret[$rv['rule']['key']] += $this->g_rules($rv[$i], $res[$i][$j]);
						}
						continue;
					}
					/* key? */
					if ( empty($rv['rule']['key']) ) {
						$_ret[$rv[$i]] = trim(strip_tags($res[$i][$j]));
					} else {
						$_ret[$rv['rule']['key']][$j][$rv[$i]] = trim(strip_tags($res[$i][$j]));
					}
				}
			}
		}

		return $_ret;
	} /* }}} */

	/**
	 * @fn
	 * @brief 取服务器在线信息，检测版本及自动更新等
	 * @param 
	 * @return 
	 * @see server/api/beat
	 */
	public function getServer() /* {{{ */
	{
		$api['api'] = 'ping';
		$api['args']['client'] = $this->client;
		wlog('client.'.date('Y-m-d').'.log', 'HQ矿机 Client v'.$this->config['version'], true);
		wlog('client.'.date('Y-m-d').'.log', '正在检测服务器...', true);
		for ( $i = 1; $i <= $this->config['priority']['retry']; $i++ ) {
			$ret = http2host($this->config['server'], json_encode($api));
			if ( $ret[0] != '-' ) {
				break;
			}
		}
		if ( $ret[0] == '-' ) {
			wlog('client.'.date('Y-m-d').'.log', '服务器离线，查找可用服务器...', true);
			for ( $i = 1; $i <= $this->config['priority']['retry']; $i++ ) {
				$ret = http2host($this->config['serverinfo']);
				if ( $ret[0] != '-' ) {
					break;
				}
			}
			if ( $ret[0] == '-' ) {
				wlog('client.'.date('Y-m-d').'.log', '未找到可用服务器！', true);
				return false;
			}
			$this->config['server'] = $ret;

			for ( $i = 1; $i <= $this->config['priority']['retry']; $i++ ) {
				$ret = http2host($this->config['server'], json_encode($api));
				if ( $ret[0] != '-' ) {
					break;
				}
			}
			if ( $ret[0] == '-' ) {
				wlog('client.'.date('Y-m-d').'.log', '服务器连接失败！', true);
				return false;
			}
		}
		$ret = json_decode($ret, true);
		if ( !empty($ret['ret']['client_version']) && $ret['ret']['client_version'] > $this->config['version'] ) {
			wlog('client.'.date('Y-m-d').'.log', '发现新版本 '.$ret['ret']['client_version'].'，正在升级...', true);
			return $this->autoUpdate();
		}
		if ( !empty($ret['ret']['version']) ) {
			wlog('client.'.date('Y-m-d').'.log', '服务器版本'.$ret['ret']['version'].'，运行正常！', true);
		}

		return true;
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 * @see server/Conf/api_config.php $api_config['key'].
	 */
	public function getClient() /* {{{ */
	{
		if ( is_file($this->config['key']) ) {
			$key = file_get_contents($this->config['key']);
		} else {
			wlog('client.'.date('Y-m-d').'.log', '-70 未找到客户key.', true);
			exit;
		}
		
		$keys = explode('@', trim($key));
		$client_id = getenv('COMPUTERNAME') ? getenv('COMPUTERNAME') : '#'.getenv('HOSTNAME');
		$this->client['client_id'] = $client_id.'@'.dirname(__FILE__);
		if ( substr(PHP_OS, 0, 3) == 'WIN' ) {	/* 解决windows中文路径乱码 */
			$this->client['client_id'] = iconv('GBK', 'UTF-8', $this->client['client_id']);
		}
		$this->client['version'] = $this->config['version'];
		$this->client['username'] = $keys[0];
		$this->client['checksum'] = md5($this->client['client_id'].'@'.$keys[1]);
		return $this->client;
	} /* }}} */

	/**
	 * @brief Returns
	 * 	defalut: $method == 'ret', $retrun = array
	 * @access protected
	 * @param $args args array or $_GET string.
	 * @param $return 'json', 'xml', 'array'
	 *!@param $method 'show', 内部调用=>'ret'
	 * @return 
	 */
	protected function doargs($action, $args = array(), $return = 'json', $method = 'ret') /* {{{ */
	{
		$ret = NULL;
		if ( !is_array($args) ) {
			$data = 'api='.$action.'&'.$args;
		} else {
			$data = array('api' => $action, 'args' => $args);
		}

		if ( $return == 'json' && is_array($data) ) {
			$ret = json_encode($data);
			$ret = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $ret);
		} else if ( $return == 'xml' ) {
			$ret = array2xml($data);
		} else {
			$ret = $data;
		}

		if ( $method == 'show' ) {
			echo $ret;
			exit;
		} else {
			/*( $method == 'ret' )*/
			return $ret;
		}
	} /* }}} */

	/**
	 * @fn
	 * @brief 采集的时候用，根据采集实际情况定制。
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
				wlog('client.'.date('Y-m-d').'.log', '-90 '.$retry.','.$i.' 连接出错: '.$html.'('.$url.')', true);
			} else if ( strlen($html) < 500 ) {
				wlog('client.'.date('Y-m-d').'.txt', '('.$url.')'.$html);
				wlog('client.'.date('Y-m-d').'.log', '-91 '.$retry.','.$i.' 采集可能出错，请告诉我们。('.$url.')', true);
			} else {
				break;
			}
		}
		return $html;
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	public function run($count = 0) /* {{{ */
	{
		$start_day = date('Y-m-d');
		/* 取服务器信息，检测更新等 */
		$server = $this->getServer();
		/** 
		 * 循环采集 
		 * 如果客户端程序连续运行超过一天，则自动退出主程序，重新开时执行并检测服务器和客户端等
		 * 如果客户端程序与服务器通信失败，则自动退出主程序，重新开时执行并检测服务器和客户端等
		 */
		$count = ($count != 0) ? $count : 999999;	/* 就算客户端机器超级牛 */
		for ( $i = 0; $i < $count; $i++ ) {
			sleep(2);
			if ( $start_day != date('Y-m-d') ) {
				return true;
			}
			if ( $this->puttask($this->dotask($this->gettask())) === false ) {
				return false;
			}
		}
		return true;
	} /* }}} */

	/**
	 * @fn
	 * @brief 不检测服务器，直接执行，并只采集一个任务包
	 * @param 
	 * @return 
	 */
	public function runOne() /* {{{ */
	{
		$ret = $this->puttask($this->dotask($this->gettask()));
		return ;
	} /* }}} */

	/**
	 * @fn
	 * @brief TODO: 自动更新客户端程序
	 * 	不是最新版本则无法采集
	 * @param 
	 * @return 
	 */
	public function autoUpdate($url = '') /* {{{ */
	{
		if ( empty($url) ) {
			$url = $this->config['server'].'/download';
		}
		$tmp_file = APP_PATH.'logs/tmp'.date('ymdHm').'.zip';
		file_put_contents($tmp_file, file_get_contents($url));
		$zip = new ZipArchive;
		$res = $zip->open($tmp_file);
		if ( $res === TRUE ) {
			$zip->extractTo(APP_PATH);
			$zip->close();
			wlog('client.'.date('Y-m-d').'.log', '更新成功！重启程序...', true);
			/* 需要重新启动 */
			exit(9);
		} else {
			wlog('client.'.date('Y-m-d').'.log', '客户端更新失败！', true);
			return false;
		}
		return true;
	} /* }}} */

}
/* end file */
