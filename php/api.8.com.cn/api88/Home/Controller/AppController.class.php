<?php
/**
 * @file AppController.class.php
 * @brief OAuth2.0 组件，本组件只提供 密码模式。
 * 	授权码模式（authorization code）
 * 	简化模式(implicit grant type) 
 *V	密码模式(Resource Owner Password Credentials Grant)
 * 	客户端模式(Client Credentials Grant)
 * @link https://en.wikipedia.org/wiki/OAuth
 * @link http://www.ruanyifeng.com/blog/2014/05/oauth_2_0.html
 *
 * Copyright (C) 2016 ZKC.com
 * All rights reserved.
 * 
 * @package Controller
 * @author Langr <hua@langr.org> 2016/05/05 15:39
 * 
 * $Id: AppController.class.php 63661 2016-09-02 09:23:54Z huanghua $
 */

namespace Home\Controller;
use Think\Controller;
class AppController extends Controller 
{
	protected $config = array();
	protected $args = array();

	protected $oauth2 = array(
		/* 需要认证(默认都需要认证) */
		'auth' => array(
			'Users' => array('info','authorize'),
			'Account' => array('moneylog'),
		),
		/**
		 * 不需要认证
		 * 支持 RESTful 风格认证
		 * GET (select),POST (create),PUT (update 完整),PATCH (update 部分),DELETE (delete)
		 * 加 method 前缀，表示此方法请求的action不用认证，
		 * 不加 method 前缀，表示此action支持的方法都不需要认证。
		 */
		'pass' => array(
			/* login,register action need https */
			'Users' => array('GET_index','POST_index','register','register_queue','resetpwd'),
 			'Token' => array('POST_index','login'),
			'Verifycode' => array('index','image','POST_sms'),
			'News' => array('detail'),
			'Orders' => array('index'),
			'Products' => array('index', 'detail', 'history'),
		)
	);

	/**
	 * @WARNNING!!! 当对外提供接口时, 出错号不应随意改变.
	 * 建议：接口功能出错号避开400-430, 使用440-499
	 */
	/* {{{ */
	const E_OK = 0;
	const E_HTTP_OK = 200;
	const E_TOKEN = 401;
	const E_IP_DENY = 403;
	const E_API_NO_EXIST = 404;
	const E_METHOD = 405;
	const E_DATA_INVALID = 420;
	const E_DATA_EMPTY = 421;
	const E_ARGS = 422;
	const E_OP_FAIL = 423;
	const E_LOGOFF = 425;
	const E_NOOP = 429;
	const E_DATA_REPEAT = 302;
	const E_SYS_1 = 903;
	const E_SYS = 905;
	const E_UNKNOW = 999;
	protected $_errmsg = array(
			self::E_OK => '',
			self::E_HTTP_OK => 'OK',
			self::E_TOKEN => 'token error!',			//'校验错误, 未受权调用!',
			self::E_IP_DENY => 'deny ip!',				//'IP被拒绝!',
			self::E_DATA_INVALID => 'invalid data!',	//'无效数据!',
			self::E_API_NO_EXIST => 'api does not exist!',//'接口不存在!',
			self::E_METHOD => 'method not allowed!',	//'请求方法不支持!',
			self::E_DATA_EMPTY => 'empty data!',		//'无数据!',
			self::E_OP_FAIL => 'operation failed!',		//'操作失败!',
			self::E_ARGS => 'arguments error!',			//'参数错误!',
			self::E_LOGOFF => 'logoff!',				//'无用户登陆信息!',
			self::E_NOOP => 'noop!',					//'无操作!',
			self::E_DATA_REPEAT => 'data repeat',		//'数据重复提交!',
			self::E_SYS_1 => 'system error 1!',			//'系统错误1, 请联系管理员!',
			self::E_SYS => 'system error!',				//'系统严重错误, 请联系管理员!',
			self::E_UNKNOW => 'unknow!',				//'未知错误!',
	);
	/* }}} */

	static $current_uid	= null;			/* 当前uid */
	static $current_appid	= null;		/* */
	static $current_token	= '';		/* 当前请求的oauth token key */
	static $current_platform = 'pc';	/* */

	protected $oauth_server = null;		/* */
	protected $data_store = null;		/* */

	function __construct() /* {{{ */
	{
		parent::__construct();
		@include(CONF_PATH.'api.php');
		//$this->config = $api_config;

		//import('Vendor.zkc.OAuth2');
		import('Vendor.zkc.OAuth2', APP_PATH, '.php');
		$this->data_store = new \DataStore();
		$this->oauth_server = new \OAuthServer($this->data_store);

		$this->startup();
	} /* }}} */

	/**
	 * @brief 
	 */
	public function startup() /* {{{ */
	{
		/* 是否需要oauth认证 */
		if (isset($this->oauth2['pass'][CONTROLLER_NAME])
			&& (in_array(ACTION_NAME, $this->oauth2['pass'][CONTROLLER_NAME]) || 
				in_array($_SERVER['REQUEST_METHOD'].'_'.ACTION_NAME, $this->oauth2['pass'][CONTROLLER_NAME]))  ) {
			$this->checkOauth();
			return true;
		}

		if ($this->checkOauth()) {
			return true;
		}

		return $this->_return(self::_error(self::E_TOKEN));
	} /* }}} */

	/**
	 * @fn
	 * @brief 
	 * @param 
	 * @return 
	 */
	protected function checkOauth() /* {{{ */
	{
		$req = \OAuthRequest::from_request();
		$this->args = $req->parameters;

		$token = $this->oauth_server->get_token($req, true);
		if ($token['errno'] == self::E_OK) {
			$key = $token['data'];
			$token_info = $this->data_store->getTokenInfo($key->key);
			if ($token_info == false) {
				return false;
			}
			self::$current_uid = $token_info['uid'];
			self::$current_appid = $token_info['app_id'];
			self::$current_token = $key->key;
		} else {
			return false;
		}
		
		return true;
	} /* }}} */

	/**
	 * @brief 检测是否登陆，在需要手动检测登陆状态时使用
	 * @param 
	 * @return true/false
	 */
	protected function _islogin() /* {{{ */
	{
		if (empty(self::$current_uid) || empty(self::$current_token)) {
			return $this->_return(self::_error(self::E_TOKEN));
			//return false;
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
		if (!is_array($data)) {
			//$data = self::_error(self::E_DATA_INVALID);
		}

		if ($return == 'json') {
			$ret = json_encode($data);
			$ret = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $ret);
		} else if ($return == 'xml') {
			$ret = array2xml($data);
		} else {
			/*( $return == 'array' )*/
			$ret = $data;
		}

		if ($method == 'show') {
			echo $ret;
			exit;
		} else if ($method == 'soap') {
			;
		} else {
			/*($method == 'ret')*/
			return $ret;
		}
		return ;
	} /* }}} */

	/**
	 * @brief error info
	 * 	_error($errno) api内部调用, 会自动触发告警信号.
	 * @access protected
	 * @param $errno 接口出错号.
	 * @param $addmsg 附加提示信息.
	 * @param $data 返回的数据.
	 * @param $warnning 是否触发告警.
	 * @return array() 返回出错号和文字解释数组.
	 */
	protected function _error($errno = 0, $addmsg = NULL, $data = array(), $warnning = true) /* {{{ */
	{
		$ret = array();
		$err = array();
		$err['errno'] = $errno;
		if (!empty($this->_errmsg[$errno])) {
			$err['errmsg'] = $this->_errmsg[$errno];
		} else {
			$err['errmsg'] = '';
		}
		if ($addmsg != NULL) {
			$err['errmsg'] = $err['errmsg'].$addmsg;
		}
		if (!empty($data)) {
			$err['data'] = $data;
		}

		if ($warnning) {
			self::warnning($errno, $err['errmsg']);
		}
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			header('HTTP/1.1 200 options method.');
			header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
			header("Access-Control-Allow-Headers: Accept, Authorization, Content-Type, Pragma, Origin, Cache-Control");
			$ret = $err;
		} else if ($errno == 0 || $errno == 200) {
			header('HTTP/1.1 200 '.$err['errmsg']);
			$ret = $err['data'];
		} else {
			header('HTTP/1.1 '.$errno.' '.$err['errmsg']);
			$ret = $err;
		}
		header("Access-Control-Allow-Origin: *");		/* 支持h5跨域调用 */
		header("Content-Type: application/json");

		return $ret;
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
	public function warnning($errno = 0, $errmsg = '', $handler = NULL, $install = 'run') /* {{{ */
	{
		static $func = array();

		if ($install == 'install') {
			$func[$errno] = $handler;
			return ;
		}
		if ($install == 'remove') {
			$func[$errno] = NULL;
			return ;
		}

		if ($handler) {
			return $handler($errno);
		}
		if (isset($func[$errno])) {
			$_func = $func[$errno];
			return $func[$errno]($errno);
		}

		/* default handler */
		switch ($errno) {
			case (self::E_OK) :
			case (self::E_HTTP_OK) :
				break;
			case (self::E_TOKEN) :
				wlog('api-token.txt', $errno.': '.$errmsg.' '.$_SERVER['REQUEST_URI']);
				break;
			case (self::E_DATA_REPEAT) :
				wlog('api-repeat.txt', $errno.': '.$errmsg.' '.$_SERVER['REQUEST_URI'].' DATA:'.print_r($_POST, true));
				break;
			case (self::E_SYS) :
			case (self::E_SYS_1) :
			case (self::E_API_NO_EXIST) :
				/* email, sms, outcall... 请随意. */
				wlog('api-warnning.txt', $errno.': '.$errmsg.' '.$_SERVER['REQUEST_URI']);
				break;
			default :
				wlog('api-error-'.CONTROLLER_NAME.'_'.ACTION_NAME.'-'.date('Ym').'.txt', $errno.': '.$errmsg.' '.$_SERVER['REQUEST_URI']."\r\nPOST:".print_r($_POST, true));
				break;
		}
		return ;
	} /* }}} */

	/**
	 * @brief empty action
	 */
	public function _empty() /* {{{ */
	{
		return $this->_return(self::_error(self::E_API_NO_EXIST, ACTION_NAME));
	} /* }}} */

	/**
	 * @brief 防重放攻击
	 */
	function _is_repeat() /* {{{ */
	{
		if (!defined('CLIENT_IP')) { define('CLIENT_IP', getenv('HTTP_X_FORWARDED_FOR') ? getenv('HTTP_X_FORWARDED_FOR') : getenv('REMOTE_ADDR')); }

		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			$post_string = '';
			return false;
		} else {
			$post_string = file_get_contents('php://input');
		}

		$chk_string = CLIENT_IP.':'.$_SERVER['REQUEST_URI'].':'.$post_string.':'.$_SERVER['HTTP_COOKIE'];
		$redis_key = 'api-repeat-'.md5($chk_string);
		$redis_value = 1;

        $redis = $this->data_store->FRedis;
		$is_repeat = $redis->get($redis_key);
		if ($is_repeat) {
			$redis->setex($redis_key, 1, $is_repeat + 1);
			return $this->_return(self::_error(self::E_DATA_REPEAT));
		} else {
			$redis->setex($redis_key, 1, $redis_value);		/* 1秒后过期 */
		}
		
		return false;
	} /* }}} */
}

/* end file */
