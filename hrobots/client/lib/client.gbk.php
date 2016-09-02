<?php
/* D:\wamp\bin\php\php5.3.13\php -f */
/**
 * @file client.php
 * @brief client �ɼ�����
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package client
 * @author Langr <hua@langr.org> 2014/04/28 16:04
 * 
 * $Id: client.gbk.php 112 2014-06-27 07:35:26Z huanghua $
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
			wlog('client.'.date('Y-m-d').'.log', '-10 ���������ӳ���'.$ret.'('.$this->config['server'].')', true);
			return false;
		}
		$_ret = json_decode($ret, true);
		if ( $_ret == false || $_ret == null || empty($_ret['ret']['do']) ) {
			wlog('client.'.date('Y-m-d').'.log', '-11 ����˷��������쳣��'.'('.$this->config['server'].')', true);
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
		wlog('client.'.date('Y-m-d').'.log', '���'.$task['module'].'('.$task['taskid'].')����:'.$count.'��, ��׼���ύ����['.$task['do'].']...', true);
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
			/* �������������� */
			wlog('client.'.date('Y-m-d').'.log', '-30 �ύ���ݳ����ٴ��ύ...'.$i, true);
			if ( $i == $this->config['priority']['retry'] ) {
				wlog('client.'.date('Y-m-d').'.log', '-31 �ύ���ݳ�����������:'.$ret);
				file_put_contents(APP_PATH.'logs/cache/'.date('Ymd-').$task['taskid'].'.txt', $post);
				return false;
			}
		}

		wlog('client.'.date('Y-m-d').'.log', '�ύ'.$task['module'].'��Ч����:'.$_ret['ret']['count'], true);
		return true;
	} /* }}} */

	/**
	 * @fn
	 * @brief ��ʼ�ɼ�����
	 * @param 
	 * @return task array.
	 */
	public function dotask(& $task) /* {{{ */
	{
		if ( $task == null || $task == false ) {
			return false;
		}
		if ( empty($task['list']) || $task['count'] == 0 ) {
			wlog('client.'.date('Y-m-d').'.log', $task['module'].' �޲ɼ�����', true);
			return false;
		}
		$count = count($task['list']);
		$do = $task['do'];
		$mod = $task['module'];
		$mod_file = APP_PATH.'module/'.$mod.'.module.php';
		if ( !is_file($mod_file) ) {
			wlog('client.'.date('Y-m-d').'.log', '-41 δ��װģ�飺'.$mod, true);
			return false;
		}
		include($mod_file);

		wlog('client.'.date('Y-m-d').'.log', '���'.$mod.'('.$task['taskid'].')����:'.$count.'��, ��ʼƴ���ɼ���['.$task['do'].']...', true);
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
		$__c = 0;				/* ʵ�ʲɼ��� */
		$__p = 0;				/* ʵ�ʲɼ���Ʒ�� */
		$ret = array();
		foreach ( $task['list'] as $r ) {
			$html = $this->read_url($r['url'], null, $mod_config['header'], $this->config['priority']['retry']);
			if ( strlen($html) < 100 ) {	/* �ɼ�ʧ�ܣ����� */
				continue;
			}
			$rk = 'gdslist';
			$rv = $mod_config['goodslist_rules']['gdslist'];
			$__w = count($rv);		/* �����ƥ����� */
			/* ��Ʒ�б�ƥ��ȫ����¼��û�������� */
			$p_flag = preg_match_all($rv['rule'], $html, $res);
			if ( $p_flag === 0 || $p_flag === false ) {
				wlog('client.'.date('Y-m-d Hi').'.log.html', $r['url'].$html);
				continue;
			}

			/* ����ƥ�䵽�ļ�¼���� */
			$_ret = array();
			$count = count($res[0]);
			$__p += $count;
			for ( $j = 0; $j < $count; $j++ ) {
				/* ÿ����¼��ƥ����� */
				for ( $i = 1; $i < $__w; $i++ ) {
					$_ret[$j][$rv[$i]] = trim(strip_tags($res[$i][$j]));
					/* TODO: ����ƥ�� */
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
		$__c = 0;				/* ʵ�ʲɼ��� */
		$_ret = array();			/* ��� */
		foreach ( $task['list'] as $r ) {
			$html = $this->read_url($r['url'], null, $mod_config['header'], $this->config['priority']['retry']);
			if ( strlen($html) < 100 ) {	/* �ɼ�ʧ�ܣ����� */
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
	 * @brief ����ͨ��ƥ�����
	 * @param 
	 * @return 
	 * @see conf/mouser.module.php $mod_config['goods_rules']
	 */
	protected function g_rules($rules, & $html) /* {{{ */
	{
		$_ret = array();			/* ��� */
		$res = array();
		foreach ( $rules as $rk => $rv ) {
			$__w = count($rv);		/* �����ƥ����� */
			/* ƥ��ȫ����¼��û�������� */
			$p_flag = preg_match_all($rv['rule']['match'], $html, $res);
			if ( $p_flag === 0 || $p_flag === false ) {
				continue;
			}

			/* ����ƥ�䵽�ļ�¼���� */
			$count = count($res[0]);
			$real_count = $count;
			if ( $rv['rule']['list'] != 'all' ) {
				$count = $rv['rule']['list'];
			}
			for ( $j = 0; $j < $count; $j++ ) {
				/* ÿ����¼��ƥ����� */
				for ( $i = 1; $i < $__w; $i++ ) {
					/* �Ӽ�ƥ�� */
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
	 * @brief ȡ������������Ϣ�����汾���Զ����µ�
	 * @param 
	 * @return 
	 * @see server/api/beat
	 */
	public function getServer() /* {{{ */
	{
		$api['api'] = 'ping';
		$api['args']['client'] = $this->client;
		wlog('client.'.date('Y-m-d').'.log', 'HQ��� Client v'.$this->config['version'], true);
		wlog('client.'.date('Y-m-d').'.log', '���ڼ�������...', true);
		for ( $i = 1; $i <= $this->config['priority']['retry']; $i++ ) {
			$ret = http2host($this->config['server'], json_encode($api));
			if ( $ret[0] != '-' ) {
				break;
			}
		}
		if ( $ret[0] == '-' ) {
			wlog('client.'.date('Y-m-d').'.log', '���������ߣ����ҿ��÷�����...', true);
			for ( $i = 1; $i <= $this->config['priority']['retry']; $i++ ) {
				$ret = http2host($this->config['serverinfo']);
				if ( $ret[0] != '-' ) {
					break;
				}
			}
			if ( $ret[0] == '-' ) {
				wlog('client.'.date('Y-m-d').'.log', 'δ�ҵ����÷�������', true);
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
				wlog('client.'.date('Y-m-d').'.log', '����������ʧ�ܣ�', true);
				return false;
			}
		}
		$ret = json_decode($ret, true);
		if ( !empty($ret['ret']['client_version']) && $ret['ret']['client_version'] > $this->config['version'] ) {
			wlog('client.'.date('Y-m-d').'.log', '�����°汾 '.$ret['ret']['client_version'].'����������...', true);
			return $this->autoUpdate();
		}
		if ( !empty($ret['ret']['version']) ) {
			wlog('client.'.date('Y-m-d').'.log', '�������汾'.$ret['ret']['version'].'������������', true);
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
			wlog('client.'.date('Y-m-d').'.log', '-70 δ�ҵ��ͻ�key.', true);
			exit;
		}
		
		$keys = explode('@', trim($key));
		$client_id = getenv('COMPUTERNAME') ? getenv('COMPUTERNAME') : '#'.getenv('HOSTNAME');
		$this->client['client_id'] = $client_id.'@'.dirname(__FILE__);
		if ( substr(PHP_OS, 0, 3) == 'WIN' ) {	/* ���windows����·������ */
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
	 *!@param $method 'show', �ڲ�����=>'ret'
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
	 * @brief �ɼ���ʱ���ã����ݲɼ�ʵ��������ơ�
	 * @param 	
	 * @return 
	 */
	protected function read_url($url, $post = null, $header = null, $retry = 5) /* {{{ */
	{
		$html = '';
		/* ���ӳ���ʱ�������n�κ����������� */
		for ( $i = 1; $i <= $retry; $i++ ) {
			$html = http2host($url, $post, $header);
			if ( $html[0] == '-' && strlen($html) < 100 ) {
				wlog('client.'.date('Y-m-d').'.log', '-90 '.$retry.','.$i.' ���ӳ���: '.$html.'('.$url.')', true);
			} else if ( strlen($html) < 500 ) {
				wlog('client.'.date('Y-m-d').'.txt', '('.$url.')'.$html);
				wlog('client.'.date('Y-m-d').'.log', '-91 '.$retry.','.$i.' �ɼ����ܳ�����������ǡ�('.$url.')', true);
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
		/* ȡ��������Ϣ�������µ� */
		$server = $this->getServer();
		/** 
		 * ѭ���ɼ� 
		 * ����ͻ��˳����������г���һ�죬���Զ��˳����������¿�ʱִ�в����������Ϳͻ��˵�
		 * ����ͻ��˳����������ͨ��ʧ�ܣ����Զ��˳����������¿�ʱִ�в����������Ϳͻ��˵�
		 */
		$count = ($count != 0) ? $count : 999999;	/* ����ͻ��˻�������ţ */
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
	 * @brief ������������ֱ��ִ�У���ֻ�ɼ�һ�������
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
	 * @brief TODO: �Զ����¿ͻ��˳���
	 * 	�������°汾���޷��ɼ�
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
			wlog('client.'.date('Y-m-d').'.log', '���³ɹ�����������...', true);
			/* ��Ҫ�������� */
			exit(9);
		} else {
			wlog('client.'.date('Y-m-d').'.log', '�ͻ��˸���ʧ�ܣ�', true);
			return false;
		}
		return true;
	} /* }}} */

}
/* end file */
