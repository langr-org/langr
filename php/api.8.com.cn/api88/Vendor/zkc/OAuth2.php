<?php
/**
 * OAuth2 ZKC模式
 * 本插件暂只处理 密码模式
 * OAuth2详情请参考 http://www.ruanyifeng.com/blog/2014/05/oauth_2_0.html
 * 
 * $Id: OAuth2.php 62761 2016-07-01 05:51:20Z huanghua $
 */

class OAuthException extends Exception 
{
	/* TODO: */
}

class OAuthConsumer 
{
	public $key;
	public $secret;

	function __construct($key, $secret, $callback_url=NULL)	
	{
		$this->key = $key;
		$this->secret =	$secret;
		$this->callback_url = $callback_url;
	}

	function __toString() 
	{
		return "OAuthConsumer[key=$this->key,secret=$this->secret]";
	}
}

/**
 * token
 */
class OAuthToken 
{
	/* access tokens & request tokens */
	public $key;
	public $secret;

	/**
	 * key = the token
	 * secret = the token secret
	 */
	function __construct($key, $secret) 
	{
		$this->key = $key;
		$this->secret =	$secret;
	}

	function to_string() 
	{
		return "oauth_token=" .	OAuthUtil::urlencode_rfc3986($this->key) . "&oauth_token_secret=" .	OAuthUtil::urlencode_rfc3986($this->secret);
	}

	function __toString() 
	{
		return $this->to_string();
	}
}

/**
 * http request 
 */
class OAuthRequest 
{
	public $parameters;
	public $http_method;
	protected $http_url;
	public $signature_str;
	public static $version = '2.0';
	public static $POST_INPUT = 'php://input';

	function __construct($http_method, $http_url, $parameters=NULL,	$signature_str='') 
	{
		$parameters	= ($parameters)	? $parameters :	array();
		$_param = array();
		parse_str(parse_url($http_url, PHP_URL_QUERY), $_param);
		$parameters	= array_merge($_param, $parameters);
		$this->parameters	= $parameters;
		//$this->parameters = $this->filter_parameters($parameters);
		$this->http_method = $http_method;
		$this->http_url	= $http_url;
		$this->signature_str = $signature_str;
	}

	/**
	 * 过滤非oauth参数
	 */
	private	function filter_parameters($parameters)	
	{
		$new_parameters	= array();
		foreach($parameters	as $key	=> $value ) {
			if ( substr($key, 0, 6) === 'oauth_' ) {
				$new_parameters[$key] =	$value;
			}
		}
		return $new_parameters;
	}

	/**
	 * OAuth 认证数据可以是 Authorization-header, GET/POST
	 */
	public static function from_request($http_method=NULL, $http_url=NULL, $parameters=NULL) 
	{
		$scheme	= (!isset($_SERVER['HTTPS']) ||	$_SERVER['HTTPS'] != "on") ? 'http' : 'https';
		$http_url = ($http_url) ? $http_url : $scheme.'://'.$_SERVER['HTTP_HOST']
					.':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		$http_method = ($http_method) ?	$http_method : $_SERVER['REQUEST_METHOD'];
		$signature_str = '';

		if (!$parameters) {
			$request_headers = OAuthUtil::get_headers();
			parse_str($_SERVER['QUERY_STRING'], $parameters);
			//$parameters = OAuthUtil::parse_parameters($_SERVER['QUERY_STRING']);

			/**
			 * 将 POST,PUT,PATCH,DELETE 传递的数据转化为$_POST
			 */
			if ($http_method != "GET" && isset($request_headers['Content-Type'])
			&& in_array($request_headers['Content-Type'], array('application/x-www-form-urlencoded','application/json'))) {
				$_param = array();
				parse_str(file_get_contents(self::$POST_INPUT), $_param);
				if ($http_method != "POST") {
					$_POST = $_param;
				}
				$parameters = array_merge($parameters, $_param);
			}

			/* 计算签名串 */
			$http_parameters = $parameters;
			unset($http_parameters['url']);
			ksort($http_parameters);
			foreach	($http_parameters as $key=>$value) {
				$pairs[] = urlencode($key) . '=' . urlencode($value);
			}
			if (!empty($pairs)) {
				$signature_str = implode('&', $pairs);
			}

			/**
			 * Authorization-header, GET/POST
	 		 * Authorization: OAuthZKC (grant_type=password&username=johndoe&password=a33r35435adfcd3&appid=a5)
			 */
			if (isset($request_headers['Authorization']) &&	substr($request_headers['Authorization'], 0, 8)	== 'OAuthZKC') {
				//$header_parameters = OAuthUtil::parse_parameters(substr(trim(substr($request_headers['Authorization'], 8)), 1, -1));
				$_param = array();
				parse_str(substr(trim(substr($request_headers['Authorization'], 8)), 1, -1), $_param);
				$parameters = array_merge($parameters, $_param);
				if (!empty($header_parameters['oauth_nonce'])) {
					//$signature_str .= 'nonce='.$header_parameters['oauth_nonce'];
				}
			}
		}

		return new OAuthRequest($http_method, $http_url, $parameters, $signature_str);
	}

	public function	set_parameter($name, $value, $allow_duplicates = true) 
	{
		if ($allow_duplicates && isset($this->parameters[$name])) {
			if (is_scalar($this->parameters[$name])) {
				$this->parameters[$name] = array($this->parameters[$name]);
			}

			$this->parameters[$name][] = $value;
		} else {
			$this->parameters[$name] = $value;
		}
	}

	public function	get_parameter($name) 
	{
		return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
	}

	public function	get_parameters() 
	{
		return $this->parameters;
	}

	public function	unset_parameter($name) 
	{
		unset($this->parameters[$name]);
	}
	
	public function	get_normalized_http_method() 
	{
		return strtoupper($this->http_method);
	}

	/**
	 * 解析url, 并重组为 scheme://host/path
	 */
	public function	get_normalized_http_url() 
	{
		$parts = parse_url($this->http_url);

		$scheme	= (isset($parts['scheme'])) ? $parts['scheme'] : 'http';
		$port =	(isset($parts['port']))	? $parts['port'] : (($scheme ==	'https') ? '443' : '80');
		$host =	(isset($parts['host']))	? $parts['host'] : '';
		$path =	(isset($parts['path']))	? $parts['path'] : '';

		if ( $port == '80' ) {
			$scheme	= 'http';
		}
		if ( $port == '443' ) {
			$scheme	= 'https';
		}
		$host =	current( explode(':', $host) );
		if ( !($scheme == 'http' && $port == '80') ) {
			$host =	"$host:$port";
		}
		return "$scheme://$host$path";
	}

	/**
	 * post to_url
	 */
	public function	to_url() 
	{
		$post_data = $this->to_postdata();
		$out = $this->get_normalized_http_url();
		if ($post_data)	{
			$out .= '?'.$post_data;
		}
		return $out;
	}

	public function	to_postdata() 
	{
		return OAuthUtil::build_http_query($this->parameters);
	}

	public function	__toString() 
	{
		return $this->to_url();
	}

	private	static function	generate_timestamp() 
	{
		return time();
	}

	/**
	 * 工具函数: 产生一个 nonce
	 */
	private	static function	generate_nonce() 
	{
		$mt = microtime();
		$rand = mt_rand();

		return md5($mt.$rand);
	}
}

/**
 * OAuth Server
 * Oauth主要接口
 */
class OAuthServer 
{
	protected $timestamp_threshold = 300;	/* 秒 */
	protected $version = '1.0';		
	protected $signature_methods = array();

	protected $data_store = null;

	function __construct($data_store) 
	{
		$this->data_store = $data_store;
	}

	/**
	 * 验证	api 调用
	 * @param $isforce bool 是否需要进行token及签名强验证
	 */
	public function	verify_request(&$request, $isforce = true) 
	{
		$consumer = $this->get_consumer($request);
		$token = $this->get_token($request, $isforce);

		if ($token['errno'] != 0) {
			return false;
		}
		return array($consumer,	$token);
	}

	/**
	 * @api get_consumer
	 * @return 0 ok;401 unauthorized
	 * 	errno = 0, data = consumer
	 */
	public function get_consumer($request) 
	{
		$consumer_key =	$request instanceof OAuthRequest
			? $request->get_parameter("oauth_consumer_key")
			: NULL;

		if (!$consumer_key) {
			return array('errno'=>401, 'errmsg'=>"Miss consumer key");
			//header('HTTP/1.1 401 Unauthorized');
			//throw new OAuthException("Miss consumer key");
		}

		$consumer = $this->data_store->lookup_consumer($consumer_key);
		if (!$consumer)	{
			return array('errno'=>401, 'errmsg'=>"Invalid consumer key");
		}

		return array('errno'=>0, 'errmsg'=>"ok", 'data'=>$consumer);
	}

	/**
	 * @api get_token
	 * @return 0 ok;401 unauthorized
	 * 	errno = 0, data = consumer
	 */
	public function get_token($request, $need_force = true) 
	{
		$token_key = $request instanceof OAuthRequest
			 ? $request->get_parameter('access_token')
			 : NULL;
		if ($need_force && !$token_key) {
			return array('errno'=>401, 'errmsg'=>"Miss token key");
			//header('HTTP/1.1 401 Unauthorized');
			//throw new OAuthException("Miss token key");
		}
		if(!empty($token_key)) {
			$token = $this->data_store->lookup_token('access', $token_key);
			if ($need_force && !$token) {
				return array('errno'=>401, 'errmsg'=>"Invalid access token");
			}
			return array('errno'=>0, 'errmsg'=>"ok", 'data'=>$token);
		}
		return array('errno'=>400, 'errmsg'=>"empty data", 'data'=>null);
	}

	/**
	 * check_signature
	 */
	private	function check_signature($request, $consumer, $token, $is_call_api=false) 
	{
		return ;
	}
}

class OAuthUtil	
{
	public static function urlencode_rfc3986($input) 
	{
		if (is_array($input)) {
			return array_map(array('OAuthUtil', 'urlencode_rfc3986'), $input);
		} else if (is_scalar($input)) {
			return str_replace(
				'+',
				' ',
				str_replace('%7E', '~',	rawurlencode($input))
			);
		} else {
			return '';
		}
	}

	public static function urldecode_rfc3986($string) 
	{
		return urldecode($string);
	}

	/**
	 * 分割http头部, 从中取出参数
	 * @see http://code.google.com/p/oauth/issues/detail?id=163
	 */
	public static function split_header($header, $only_allow_oauth_parameters = true) 
	{
		/* 去掉参数外面的括号 ()*/
		$header = substr(trim($header), 1, -1);
		return OAuthUtil::parse_parameters($header);
	}

	/**
	 * @fn
	 * @brief get http headers
	 * @return 
	 */
	public static function get_headers() 
	{
		if (function_exists('apache_request_headers')) {
			/* Authorization: header */
			$headers = apache_request_headers();

			$out = array();
			foreach	($headers AS $key => $value) {
				$key = str_replace(" ",	"-", ucwords(strtolower(str_replace("-", " ", $key))));
				$out[$key] = $value;
			}
		} else {
			$out = array();
			if( isset($_SERVER['CONTENT_TYPE']) )
				$out['Content-Type'] = $_SERVER['CONTENT_TYPE'];
			if( isset($_ENV['CONTENT_TYPE']) )
				$out['Content-Type'] = $_ENV['CONTENT_TYPE'];

			foreach	($_SERVER as $key => $value) {
				if (substr($key, 0,	5) == "HTTP_") {
					$key = str_replace(" ",	"-", ucwords(strtolower(str_replace("_", " ", substr($key, 5)))));
					$out[$key] = $value;
				}
			}
		}
		return $out;
	}

	/**
	 * 将url参数a=b&c=d&e=f解析成数组
	 * array('a' =>	array('b','c'),	'd' => 'e')
	 */
	public static function parse_parameters($input)
	{
		if (!isset($input) || !$input) return array();

		$pairs = explode('&', $input);

		$parsed_parameters = array();
		foreach	($pairs	as $pair) {
			$split = explode('=', $pair, 2);
			$parameter = OAuthUtil::urldecode_rfc3986($split[0]);
			$value = isset($split[1]) ? OAuthUtil::urldecode_rfc3986($split[1]) : '';

			if (isset($parsed_parameters[$parameter])) {
				if (is_scalar($parsed_parameters[$parameter])) {
					$parsed_parameters[$parameter] = array($parsed_parameters[$parameter]);
				}
				$parsed_parameters[$parameter][] = $value;
			} else {
				$parsed_parameters[$parameter] = $value;
			}
		}
		return $parsed_parameters;
	}

	public static function build_http_query($params) 
	{
		if (!$params) { return ''; }

		$keys =	OAuthUtil::urlencode_rfc3986(array_keys($params));
		$values	= OAuthUtil::urlencode_rfc3986(array_values($params));
		$params	= array_combine($keys, $values);

		uksort($params,	'strcmp');

		$pairs = array();
		foreach	($params as $parameter => $value) {
			if (is_array($value)) {
				sort($value, SORT_STRING);
				foreach	($value	as $duplicate_value) {
					$pairs[] = $parameter.'='.$duplicate_value;
				}
			} else {
				$pairs[] = $parameter.'='.$value;
			}
		}
		return implode('&', $pairs);
	}
}

/**
 * OAuth DataStore Model
 * Redis
 */
class DataStore	{
	public $name		= 'DataStore';
	public $useTable	= false;

	public $FRedis		= null;			/* redis vendor */
	public $tokenPrefix	= 'oauth_tokens';
	public $tokenRevPrefix	= 'oauth_uid';
	public $productPrefix	= 'product_key';

	function __construct() 
	{
		/* cakephp */
		//App::uses('FRedis', 'Vendor');
		/* thinkphp */
		import('Vendor.zkc.FRedis', APP_PATH, '.php');
		$this->FRedis = FRedis::getCacheSingleton();
	}

	/**
	 * 根据consumerKey创建 OAuthConsumer object
	 * @param string $consumerKey API key
	 * @return object OAuthConsumer	object || null
	 */
	public function lookup_consumer($consumerKey)
	{
		$consumerData = $this->getConsumer($consumerKey);
		if ($consumerData) {
			return new OAuthConsumer($consumerData['key'], $consumerData['secret']);
		}
		return null;
	}
	
	/**
	 * 获取指定 consumer和 token key对应的 token secret
	 * @param string request or access
	 * @param string $token	token_key
	 * @return object oauth_token 返回一個oauth_token object
	 */
	public function lookup_token($token_type, $token_key)
	{
		$tokenData = $this->getTokenInfo($token_key);
		if ($tokenData)	{
			return new OAuthToken($token_key, $tokenData['secret']);
		}
		return null;
	}
	
	public function lookup_nonce($consumer, $token, $nonce, $timestamp) 
	{
		return $nonce;
	}
	
	/**
	 * 获取指定token的最后请求时间(对应updated字段)
	 * @param object $token	oauth_token
	 */
	public function get_last_timestamp($token) 
	{
		$token_key = $token->key;
		if ($tokenData = $this->getTokenInfo($token_key)) {
			return $tokenData['updated'];
		}
	}

	/* redis op */

	/**
	 * 根据productKey获取consumer信息（先从redis上查找，如果不存在则查找mysql model）
	 * NOTE: 因为项目接口只为公司内部app,pc,h5提供接口，并无外部接口计划，
	 * 	所以(接入)客户端信息暂不区分和保存。
	 * @param String $productKey
	 * @return string $ConsumerInfo
	 */
	public function getConsumer($productKey)
	{
		$key = KEY_PREFIX.':'.$this->productPrefix.':'.$productKey;

		$ConsumerInfo = $this->FRedis->hGetAll($key);
		if ($ConsumerInfo) {
			return $ConsumerInfo;
		}

		/* TODO: 从数据库中读取应用配置 */
		$ConsumerInfo =	array('id'=>88,	'key'=>'35G65672Khm441', 'secret' => 'asg54y3elhfh3ilc34ctv24225l');
		/* 添加到Redis */
		$this->FRedis->hMset($key, $ConsumerInfo);

		return $ConsumerInfo;
	}

	/**
	 * 根据tokenKey获取app id
	 * @param String $tokenKey
	 */
	public function getAppIdByToken($tokenKey)
	{
		$key = KEY_PREFIX.':'.$this->tokenPrefix.':'.$tokenKey;
		return $this->FRedis->hGet($key, 'app_id');
	}

	/**
	 * 获取token记录的所有字段信息
	 * @param string $tokenKey token key
	 */
	public function getTokenInfo($tokenKey) 
	{
		$key = KEY_PREFIX.':'.$this->tokenPrefix.':'.$tokenKey;
		if ($tokenInfo = $this->FRedis->hGetAll($key)) {
			return $tokenInfo;
		}
		return false;
	}

	/**
	 * 根据uid反查token
	 */
	public function getRevTokenInfo($uid) 
	{
		$key = KEY_PREFIX.':'.$this->tokenRevPrefix.':'.$uid;
		if ($tokenInfo = $this->FRedis->hGetAll($key)) {
			return $tokenInfo;
		}
		return false;
	}

	/**
	 * 设置token
	 */
	public function setAccessToken($uid, $app_id, $token_key, $token_secret = '', $expires = 3600)
	{
		if (empty($uid) || $uid < 1) {
			return false;
		}
		$key = KEY_PREFIX.':'.$this->tokenPrefix.':'.$token_key;
		$data =	array(
				'app_id'	=> $app_id,
				//'type'	=> 'access',
				//'key'		=> $token_key,
				'secret'	=> $token_secret,
				'authorized'	=> 1,
				'updated'	=> time(),
				'uid'		=> $uid
		);
		
		if ($this->FRedis->hMset($key, $data)) {
			/* 设置过期时间 */
			$this->FRedis->setTimeout($key, $expires);
			
			/* 设置反表 */
			$rev_key = KEY_PREFIX.':'.$this->tokenRevPrefix.':'.$uid;
			$rev_data = array(
				'app_id'	=> $app_id,
				'token_key'	=> $token_key,
				'authorized'	=> 1,
				'uid'		=> $uid,
			);
			$ret = $this->FRedis->hMset($rev_key, $rev_data);

			return true;
		} else {
			return false;
		}
	}

	/**
	 * 注册时直接取得accessToken
	 */
	public function registerGetAccessToken($uid, $app_id, $expires = 3600)
	{
		$token_key  = $this->generateKey(true);
		$secret	= $this->generateKey();
		$ret = $this->setAccessToken($uid, $app_id, $token_key, $secret, $expires);
		if ($ret) {
			return array('key' => $token_key, 'secret' => $secret);
		}
		return false;
	}

	/**
	 * 更新用户token
	 */
	public function refreshAccessToken($uid, $app_id, $expires = 3600)
	{
		/* 先删除原来token和反查token */
		$this->delAccessToken($uid);

		$token_key = $this->generateKey(true);
		$secret	= $this->generateKey();

		$ret = $this->setAccessToken($uid, $app_id, $token_key,	$secret, $expires);
		if ($ret) {
			return array('key' => $token_key, 'secret' => $secret);
		}
		return false;
	}

	/**
	 * 清除用户token
	 */
	public function delAccessToken($uid)
	{
		/* 先删除原来token和反查token */
		$old_token_info	= $this->getRevTokenInfo($uid);
		if (!empty($old_token_info)) {
			$old_key = KEY_PREFIX.':'.$this->tokenPrefix.':'.$old_token_info['token_key'];
			$this->FRedis->del($old_key);
			$old_rev_key = KEY_PREFIX.':'.$this->tokenRevPrefix.':'.$uid;
			$this->FRedis->del($old_rev_key);
		}

		return true;
	}

	/**
	 * 产生一个唯一key
	 * @param boolean $unique force	the key to be unique
	 * @return string
	 */
	function generateKey($unique =	false)
	{
		$key = md5(uniqid(rand(), true));
		if ($unique) {
			list($usec,$sec) = explode(' ',microtime());
			$key .=	dechex($usec).dechex($sec);
		}
		return $key;
	}
}

/* end file */
