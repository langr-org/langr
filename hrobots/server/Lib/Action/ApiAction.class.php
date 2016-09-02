<?php
/**
 * @file ApiAction.class.php
 * @brief Hrobots 采集程序 服务器端 接口
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package Action
 * @author Langr <hua@langr.org> 2014/04/14 14:54
 * 
 * $Id: ApiAction.class.php 113 2014-06-27 07:36:30Z huanghua $
 */

class ApiAction extends Action
{
	protected $config = array();
	/**
	 * @WARNNING!!! 当对外提供接口时, 出错号不应随意改变.
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
	protected $_errmsg = array(
			self::E_OK => 'ok!',
			self::E_ARGS => '参数错误!',
			self::E_LOGOFF => '无用户登陆信息!',
			self::E_DATA_INVALID => '无效数据!',
			self::E_NOOP => '无操作!',
			self::E_IP_DENY => 'IP被拒绝!',
			self::E_DATA_EMPTY => '无数据!',
			self::E_OP_FAIL => '操作失败!',
			self::E_CHECKSUM => '校验错误, 未受权调用!',
			self::E_VERSION => '不支持的版本号，请升级程序!',
			self::E_API_NO_EXIST => '接口不存在!',
			self::E_SYS_1 => '系统错误1, 请联系管理员!',
			self::E_SYS => '系统严重错误, 请联系管理员!',
			self::E_UNKNOW => '未知错误!',
			);
	/* }}} */

	function __construct() /* {{{ */
	{
		parent::__construct();
		@include(CONF_PATH.'api_config.php');

		$this->config = $api_config;
		$this->config['allow'] = strlen($api_config['allow']) ? $api_config['allow'] : '0';
	} /* }}} */

	/**
	 * @fn
	 * @brief api 主入口
	 * @NOTE 当前设计只开放提供 index action, 
	 * 	其他的 action 需要通过 index 来调用执行.
	 * 	参数全部通过json格式POST到 index.
	 * @access action
	 * @param $args['api'] 请求的action, 必需.
	 * @param $args['username'] 采集客户身份
	 * @param $args['client_id'] 采集机器身份
	 * @param $args['client_key'] 采集客户key
	 *!@param $args['do'] collect|update|null 采集行为: 采集或更新, 默认为空
	 *!@param $args['module'] null|[digikey,mouser,future,element14,avnet,arrow,rs,tti] 默认为空
	 *!@param $args['return'] 返回数据格式: ajax(默认), xml.
	 * @return action return. 
	 */
	public function index() /* {{{ */
	{
		/* client post data: */
		$data = file_get_contents('php://input');
		$args = json_decode($data, true);
		if ( !count($args) || empty($args['api']) || !isset($args['args']) ) {
			return $this->_return($this->_error(self::E_ARGS));
		}
		$action = $args['api'];		/* action */
		$args = $args['args'];		/* args */
		$client = $args['client'];
		$client['device'] = isset($client['device']) ? json_encode($client['device']) : '';
		$client['ip'] = CLIENT_IP;
		$client['status'] = '2';
		$client['created'] = time();
		$log = M('client_log');
		if ( !$this->dochecksum($client) ) {
			$client['note'] = self::errmsg(self::E_CHECKSUM);
			$taskid = $log->add($client);
			return $this->_return(self::_error(self::E_CHECKSUM));
		}
		if ( !$this->checkversion($client['version']) ) {
			$client['note'] = self::errmsg(self::E_VERSION);
			$taskid = $log->add($client);
			return $this->_return(self::_error(self::E_VERSION));
		}

		/**
		 * 客户端第一次不提供采集模块参数时，服务器端随机给客户端发送任务
		 * 客户端之后在有提供模块参数时，服务器端按给定参数分发任务，
		 * 如果此模块无任务，客户端自动按顺序换下一个采集模块
		 */
		$args['do'] = empty($args['do']) ? $this->priority('do') : $args['do'];
		$args['module'] = empty($args['module']) ? $this->priority('module') : $args['module'];

		if ( method_exists($this, $action) ) {
			return $this->$action($args);
		}
		return $this->_return(self::_error(self::E_API_NO_EXIST));
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @access action
	 * @param 
	 * @return 
	 */
	public function _empty() /* {{{ */
	{
		return $this->_return(self::_error(self::E_API_NO_EXIST));
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @access action
	 * @param 
	 * @return 
	 */
	public function download() /* {{{ */
	{
		$file = $this->config['client_zip'];
		if ( !empty($_GET['type']) && $_GET['type'] == 'full' ) {
			$file = DATA_PATH.$file;
		} else {
			$file = DATA_PATH.substr($file, 0, -9).'.update.zip';
		}
		$data = file_get_contents($file);
		header("Content-type: application/octet-stream");
		header("Accept-Ranges: bytes");
		header("Accept-Length: ".strlen($data));
		header("Content-Disposition: attachment; filename=".basename($file));
		echo $data;
		exit;
	} /* }}} */

	/**
	 * @fn
	 * @brief 客户端请求任务包
	 * @access action
	 * @param $args['username'] 采集客户身份
	 * @param $args['client_id'] 采集机器身份
	 * @param $args['client_key'] 采集客户key
	 * @param $args['do'] 
	 * @param $args['module'] 
	 *!@param $args['return'] 返回数据格式: ajax(默认), xml.
	 * @return json:{"errno":0,"errmsg":"ok",
	 * 		"ret":{"do":"update","module":"mouser","psize":100,"data":[{"id":123,"url":""}]}}
	 */
	public function gettask(& $args = array()) /* {{{ */
	{
		if ( empty($args['do']) || empty($args['module']) ) {
			return $this->_return(self::_error(self::E_NOOP));
		}
		$now = time();
		$_ret = array();

		/* 取任务包 */
		if ( $args['do'] == 'collect' ) {
			$task_tab = $args['module']."_goodslist_url";
			$url = 'gdslist_url';
		} else if ( $args['do'] == 'update' ) {
			$task_tab = $args['module']."_goods_url";
			$url = 'gds_url';
		}
		$m = M($task_tab);
		$r = $m->where("status='0' and src='{$args['module']}' and updated<".($now - $this->config['priority']['time']))->field("id,$url url")->limit($this->config['priority']['task'])->select();

		$client = $args['client'];
		$client['device'] = isset($client['device']) ? json_encode($client['device']) : '';
		$client['ip'] = CLIENT_IP;
		$client['module'] = $args['module'];
		$client['do'] = $args['do'];
		$client['get'] = count($r);
		$client['created'] = $now;
		$c_res = count($r);
		if ( $c_res > 0 ) {
			$log = M('client_log');
			$taskid = $log->add($client);
		}

		$where = 'id in ( ';
		foreach ( $r as $_v ) {
			$where .= $_v['id'].',';
		}
		$where = substr($where, 0, -1).')';
		/* 正在采集 */
		$m->where($where)->save(array('client'=>$taskid,'status'=>'1','updated'=>$now));

		$_ret['do'] = $args['do'];
		$_ret['module'] = $args['module'];
		$_ret['taskid'] = $taskid;
		$_ret['count'] = $c_res;
		$_ret['list'] = $r;
		$ret = self::_error(self::E_OK);
		$ret['ret'] = $_ret;
		return $this->_return($ret);
	} /* }}} */

	/**
	 * @fn
	 * @brief 客户端回送完成的任务数据
	 * @access action
	 * @param $args['do'] .
	 *!@param $args['return'] 返回数据格式: ajax(默认), xml.
	 *!@param $args['ret'] 发过来的数据: 
	 * 		{"do":"update","module":"mouser","taskid":123,"count":"100","list":[{"id":123,"url":"","k1":"v1"}]}
	 * @return 
	 */
	public function puttask(& $args = array())  /* {{{ */
	{
		if ( empty($args['do']) || empty($args['module']) ) {
			return $this->_return(self::_error(self::E_NOOP));
		}
		/* 处理任务包 */
		if ( $args['do'] == 'collect' ) {
			$args['count'] = $this->collect($args);
		} else if ( $args['do'] == 'update' ) {
			$args['count'] = $this->update($args);
		}
		$now = time();

		$log['updated'] = $now;
		$log['put'] = $args['count'];
		$log['status'] = 1;			/* 采集完成返回 */
		$mlog = M('client_log');
		$mlog->where('id='.$args['taskid'])->save($log);

		unset($args['list']);
		unset($args['client']);
		$ret = self::_error(self::E_OK);
		$ret['ret'] = $args;
		return $this->_return($ret);
	} /* }}} */

	/**
	 * @fn
	 * @brief 采集结果处理函数
	 * @access public
	 * @param $task
	 * @return 
	 */
	public function collect(& $task = array())  /* {{{ */
	{
		/* 处理任务包 */
		if ( $task['do'] != 'collect' ) {
			return 0;
		}
		$task_tab = $task['module']."_goodslist_url";
		$now = time();
		$m = M($task['module']."_goods_url");
		//$m = new GoodsUrlModel($task['module']);
		$m2 = M($task['module'].'_goods');

		$_r = 0;
		$task_where = 'id in ( ';
		$v_all = array();
		$v2_all = array();
		foreach ( $task['list'] as $v ) {
			$_c = count($v);
			$_r += $_c;
			$v2 = array();
			if ( $_c ) {
				$task_where .= $v[0]['lid'].',';
			}
			for ( $i = 0; $i < $_c; $i++ ) {
				$v[$i]['src'] = $task['module'];
				$v[$i]['created'] = $now;
				$v2[$i]['gds_name'] = $v[$i]['gds_name'];
				$v2[$i]['src'] = $task['module'];
				$v2[$i]['gds_url'] = $v[$i]['gds_url'];
				$v2[$i]['provider_url'] = $v[$i]['provider_url'];
				$v2[$i]['provider'] = $v[$i]['provider'];
				$v2[$i]['inventory'] = $v[$i]['inventory'];
				$v2[$i]['created'] = $now;
				unset($v[$i]['provider_url']);
				unset($v[$i]['provider']);
				unset($v[$i]['inventory']);
				//$m->relation(true)->add($v[$i]);	/* 太慢 */
			}
			$v_all = array_merge($v_all, $v);
			$v2_all = array_merge($v2_all, $v2);
		}
		$m->addAll($v_all, null, true);
		$m2->addAll($v2_all, null, true);
		$task_where = substr($task_where, 0, -1).')';
		/* update... */
		$m = M($task_tab);
		$m->where($task_where)->save(array('status'=>'2','updated'=>$now));
		/* NOTE: 因执行时间过长，此设计改为后台或手动处理 */
		/* update `cot_mouser_goods` g,`cot_mouser_goods_url` gl set g.gid=gl.id where g.gds_url=gl.gds_url and g.gid!=gl.id */
		return $_r;
	} /* }}} */

	/**
	 * @fn
	 * @brief 更新结果处理函数
	 * @access public
	 * @param $task
	 * @return 
	 */
	public function update(& $task = array())  /* {{{ */
	{
		/* 处理任务包 */
		if ( $task['do'] != 'update' ) {
			return 0;
		}
		$task_tab = $task['module']."_goods_url";
		$now = time();
		$m = M($task['module']."_goods");
		//$m = new GoodsModel($task['module']);

		$_r = 0;
		$task_where = 'id in ( ';
		$v_all = array();
		foreach ( $task['list'] as $v ) {
			$v['gid'] = $v['id'];
			unset($v['id']);
			$task_where .= $v['gid'].',';

			$v['updated'] = $now;
			$v['status'] = 2;
			/* 特殊栏位处理 e.g. prices, gds_attrs */
			if ( isset($v['prices']) ) {
				$tmp = array();
				$v['note'] = $v['prices'][0]['price_p'];	/* 价格单位或符号 */
				foreach ( $v['prices'] as $p ) {
					$tmp[$p['price_c']] = $p['price_v'];
				}
				$v['prices'] = json_encode($tmp);
			}
			if ( isset($v['gds_attrs']) ) {
				$tmp = array();
				foreach ( $v['gds_attrs'] as $p ) {
					$tmp[$p['attr_n']] = $p['attr_v'];
				}
				$v['gds_attrs'] = json_encode($tmp);
			}
			//$m->relation(true)->save($v);
			$m->where('gid='.$v['gid'])->save($v);
			$_r++;
		}
		$task_where = substr($task_where, 0, -1).')';
		/* update... */
		$m = M($task_tab);
		$m->where($task_where)->save(array('cot_count'=>array('exp', 'cot_count+1'),'status'=>'2','updated'=>$now));
		return $_r;
	} /* }}} */

	/**
	 * @fn
	 * @brief 调度策略函数
	 * @access public
	 * @param $p 'do','module'
	 * @return 采集动作和模块
	 */
	public function priority($p = 'module')  /* {{{ */
	{
		if ( !empty($this->config['priority'][$p]) ) {
			return $this->config['priority'][$p];
		}
		/*if ( $p == 'do' ) {
			return $this->config['collect_do'][rand(0, count($this->config['collect_do'])-1)];
		}
		if ( $p == 'module' ) {
			return $this->config['collect_module'][rand(0, count($this->config['collect_module'])-1)];
		}*/
		return $this->config['collect_'.$p][rand(0, count($this->config['collect_'.$p]) - 1)];
	} /* }}} */

	/**
	 * @fn
	 * @brief 检测服务器是否在线
	 * @access action
	 *!@param $args['return'] 返回数据格式: ajax(默认), xml.
	 * @return 
	 */
	public function ping(&$args = array())  /* {{{ */
	{
		$ret = $this->_error(self::E_OK);
		$ret['ret'] = array('version' => $this->config['version'],
			'client_version' => $this->config['client_version'],
			'time' => date('YmdHis'));
		return $this->_return($ret);
	} /* }}} */

	/**
	 * @fn
	 * @brief 服务器心跳程序，服务器放在动态ip环境时需要定时执行；
	 * 	向某个公网固定服务器汇报自己的ip和路径，方便客户端找到服务器。
	 * 	$api_config['server_beat'] 指向的程序：
	 * 		'?act=beat&name=hrobots&path=' 为汇报服务器ip和路径
	 * 		'?act=serverinfo&name=hrobots' 为获取服务器ip和路径
	 * 		不带参数会直接显示服务器外网真实ip。
	 * @access action
	 * @return 
	 */
	public function beat()  /* {{{ */
	{
		if ( empty($this->config['server_beat']) ) {
			return false;
		}
		$path = '?act=beat&name=hrobots&path=';
		$real_ip = file_get_contents($this->config['server_beat']);
		if ( empty($real_ip) || strlen($real_ip) < 7 ) {
			return false;
		}
		if ( !empty($this->config['server_path']) ) {
			$path .= 'http://'.$real_ip.$this->config['server_path'];
		}
		$ret = file_get_contents($this->config['server_beat'].$path);
		return true;
	} /* }}} */

	/**
	 * @fn
	 * @brief checksum
	 * 	远程调用时的鉴权, 如果IP为信任IP, 则直接返回 true.
	 * 	为了提高鉴权校验效率, 一个陌生的IP, 只有第一次请求时需要鉴权, 通过后下次为信任.
	 * 	!!!高效率鉴权只在有正常传送COOKIE的情况有效.
	 * @access protected
	 * @param $args['checksum'] checksum = md5(client_id+'@'+client_key).
	 * 	client_id 由客户端取得并传到服务端
	 * 	client_key = md5(username+'@'+key) client_key 在服务器端生成并保存在客户端，客户端不知校检key
	 * 	username, client_key 存放在 client.key 中: username@client_key
	 * @see api_config.php $api_config['key']
	 * @return true, success; false, failure.
	 */
	protected function dochecksum(&$args = array())  /* {{{ */
	{
		if ( defined('API_DEBUG') && API_DEBUG ) {
			return true;	/* debug */
		}
		
		$allow = isset($_COOKIE['hrobots_api_ip']) ? $_COOKIE['hrobots_api_ip'] : '';
		/* 上次刚刚校验过, 此次pass */
		if ( !empty($allow) && $allow == md5(getenv('REMOTE_ADDR')) ) {
			//return true;
		}
		/* 信任的IP地址, pass */
		if ( $this->config['allow'] == '*' || strpos($this->config['allow'], getenv('REMOTE_ADDR')) !== false ) {
			return true;
		}

		$sum = md5($args['client_id'].'@'.md5($args['username'].'@'.$this->config['key']));
		if ( $sum != $args['checksum'] ) {
			return false;
		}

		setcookie('hrobots_api_ip', md5(getenv('REMOTE_ADDR')), 0, '/');
		return true;
	} /* }}} */

	/**
	 * @brief 检测是否允许的客户端版本
	 * @param client version.
	 * @return true allow, false deny.
	 */
	protected function checkversion($version)  /* {{{ */
	{
		if ( strpos($this->config['allow_client'], $version) === false ) {
			return false;
		}

		return true;
	} /* }}} */

	/**
	 * @brief Returns
	 * 	defalut: $method == 'ret', $retrun = array
	 * @access protected
	 * @param $data data array.
	 * @param $return 'json', 'xml', 'array'
	 *!@param $method GET/POST=>'show', 'soap', 内部调用=>'ret'
	 * @return 
	 */
	protected function _return($data = array(), $return = 'json', $method = 'show') /* {{{ */
	{
		$ret = NULL;
		if ( !is_array($data) ) {
			$data = self::_error(self::E_DATA_INVALID);
		}

		if ( $return == 'json' ) {
			$ret = json_encode($data);
			$ret = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $ret);
		} else if ( $return == 'xml' ) {
			$ret = array2xml($data);
		} else {
			/*( $return == 'array' )*/
			$ret = $data;
		}

		if ( $method == 'show' ) {
			echo $ret;
			exit;
		} else if ( $method == 'soap' ) {
			;
		} else {
			/*( $method == 'ret' )*/
			return $ret;
		}
		return ;
	} /* }}} */

	/**
	 * @brief error info
	 * 	errmsg($errno) 外部或应用层调用
	 * 	_error($errno) api内部调用
	 * @param $errno 接口出错号.
	 * @return string 返回出错号的文字解释. 
	 */
	public function errmsg($errno = 0, $addmsg = NULL) /* {{{ */
	{
		$ret = self::_error($errno, $addmsg, false);
		return $ret['errmsg'];
	} /* }}} */

	/**
	 * @brief error info
	 * 	_error($errno) api内部调用, 会自动触发告警信号.
	 * @access protected
	 * @param $errno 接口出错号.
	 * @param $addmsg 附加提示信息.
	 * @param $warnning 是否触发告警.
	 * @return array() 返回出错号和文字解释数组.
	 */
	protected function _error($errno = 0, $addmsg = NULL, $warnning = true) /* {{{ */
	{
		$err = array();
		if ( $warnning ) {
			self::warnning($errno);
		}

		if ( empty($this->_errmsg[$errno]) ) {
			$errno = self::E_UNKNOW;
		}
		$err['errno'] = $errno;
		$err['errmsg'] = $this->_errmsg[$errno];
		if ( $addmsg != NULL ) {
			$err['errmsg'] = $err['errmsg'].$addmsg;
		}
		$err['ret'] = '';

		return $err;
	} /* }}} */

	/**
	 * @brief 自动告警处理.
	 * 	handler callback function type: doerror($errno).
	 * @access public
	 * @param $errno 接口出错号, (告警级别).
	 * @param $handler callback, 自定义安装的告警处理程序.
	 * @param $install 'run', 'install', 'remove', 安装或移除自定义告警, 
	 * 	同时指定 $handler 并且 $install 为 'run' 时为临时一次性安装告警程序
	 * @return void. 
	 * @static
	 */
	public function warnning($errno = 0, $handler = NULL, $install = 'run') /* {{{ */
	{
		static $func = array();

		if ( $install == 'install' ) {
			$func[$errno] = $handler;
			return ;
		}
		if ( $install == 'remove' ) {
			$func[$errno] = NULL;
			return ;
		}

		if ( $handler ) {
			return $handler($errno);
		}
		if ( isset($func[$errno]) ) {
			$_func = $func[$errno];
			return $func[$errno]($errno);
		}

		/* default handler */
		switch ($errno) {
		case (self::E_SYS) :
		case (self::E_SYS_1) :
		case (self::E_API_NO_EXIST) :
			/* email, sms, outcall... 请随意. */
			wlog('api-warnning.txt', "E_SYS:$errno: ".self::errmsg($errno)."\r\n".$_SERVER['REQUEST_URI']."\r\nPOST:".print_r($_POST, true));
			break;
		default :
			break;
		}
		return ;
	} /* }}} */
}
/* end file */
