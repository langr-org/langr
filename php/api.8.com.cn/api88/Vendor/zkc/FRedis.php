<?php
/**
 * 使用方法：
 *	App::uses('FRedis', 'Vendor');
 *	1. $this->FRedis = FRedis::getSingleton();	//持久化
 *	2. $this->FRedis = FRedis::getCacheSingleton();	//非持久化
 */
class FRedis 
{
	private	static $_instance =	null;	//持久化实例，用作落地存储
	private	static $_CacheInstance = null;	//非持久化，用作临时cache存储

	public $hash = null;
	public $redis =	null;
	public $connectPool	= null;	 //连接池
	public $counts = 0;
	var $servers = array();

	private	function __construct() {
		$this->hash = new Flexihash();
	}

	/**
	 * getCacheSingleton : 非持久化实例
	 */
	public static function getCacheSingleton() {
		if ( !isset( self::$_CacheInstance ) ) {
			//create new instance
			require_once 'flexihash-0.1.9.php';
			self::$_CacheInstance =	new FRedis;
		}

		self::$_CacheInstance->addServers( false );
		return self::$_CacheInstance;
	}
	
	/**
	 * getSingleton	: 持久化实例
	 */
	public static function getSingleton($cacheservername = '') {
		if ( !isset( self::$_instance ) ) {
			//create new instance
			require_once 'flexihash-0.1.9.php';
			self::$_instance = new FRedis;
		}

		self::$_instance->addServers( true ,$cacheservername);
		return self::$_instance;
	}
	
	/**
	 * 根据config映射具体的实例ip&port
	 */
	public function addServers($isPersistent=true, $cacheservername = '') {
		//缓存数据是否持久化
		if (empty($cacheservername)) {
			if ( $isPersistent ) {
				$this->servers = C('REDIS_SERVER');
			}else {
				$this->servers = array(
					array('host'=>C('REDIS_HOST'),
					'port'=>C('REDIS_PORT'),
					'auth'=>C('REDIS_AUTH'))
				);
			}
		} else {
			$this->servers = C($cacheservername);
		}
		//print_r($this->servers);
		foreach ( $this->servers as $server ) {
			$node = $server['host'] . ':' . $server['port'] . ':' . $server['auth'];
			$this->$node = false;
			$targets[] = $node;
		}
		if ( !$this->hash->getAllTargets() ) {
			$this->hash->addTargets( $targets );
		}
	}
	
	public function showServer() {
		$results = $this->hash->getAllTargets();
		return isset( $results )?$results:array();
	}

	/*
	 * 方便调试
	 * 根据key获取nodes
	 */
	public function getNodesBykey($key) {
		return $this->hash->lookupList($key, 1);
	}

	/**
	 * Redis的统一调用,但保证$arguments[0]为KEY
	 *
	 * @param string $name
	 * @param array $arguments
	 */
	function __call( $name, $arguments ) {
		if ( !isset( $name ) && !isset( $arguments[0] ) && empty( $arguments[0] ) ) {
			return false;
		}
		$nodes = $this->hash->lookupList( $arguments[0], 1 );
		//print_r($nodes);
		foreach ( $nodes as $node ) {
			if ( !isset( $this->connectPool[$node] ) || empty( $this->connectPool[$node] ) ) {
				$server = explode( ':', $node );
				$this->connectPool[$node] = new Redis();
				// 连接Redis失败时
				if($this->connectPool[$node]->connect( $server[0], $server[1] )	== false){
					//error log
				}
				if(!empty($server[2])) {
					$this->connectPool[$node]->auth($server[2]);
				}
			}
			if ( $this->connectPool[$node] ) {
				$value = call_user_func_array(array($this->connectPool[$node], $name), $arguments);
				return $value;
			}
		}
		return false;
	}
}

/* end file */
