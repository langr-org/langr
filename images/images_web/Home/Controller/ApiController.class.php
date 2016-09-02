<?php
/**
 * @file ApiController.class.php
 * @brief 
 * 
 * Copyright (C) 2014 ZKC.com
 * All rights reserved.
 * 
 * @package Controller
 * @author Langr <hua@langr.org> 2014/10/23 09:36
 * 
 * $Id: ApiController.class.php 810 2014-10-28 08:04:21Z huanghua $
 */

namespace Home\Controller;
use Think\Controller;
class ApiController extends Controller
{
	protected $config = array();
	protected $logfile = 'upload.api.log';
	/**
	 * @WARNNING!!! 当对外提供接口时, 出错号不应随意改变.
	 */
	/* {{{ */
	const E_OK = 0;
	const E_ARGS = 100;
	const E_DATA_INVALID = 103;
	const E_NOOP = 104;
	const E_IP_DENY = 105;
	const E_DATA_EMPTY = 110;
	const E_OP_FAIL = 111;
	const E_CHECKSUM = 150;
	const E_KEY_NO_EXIST = 403;
	const E_API_NO_EXIST = 404;
	const E_MAX_SIZE = 410;
	const E_INVALID_TYPE = 411;
	const E_SYS_1 = 503;
	const E_SYS = 505;
	const E_UNKNOW = 999;
	protected $_errmsg = array(
			self::E_OK => 'ok!',
			self::E_ARGS => '参数错误!',
			self::E_DATA_INVALID => '无效数据!',
			self::E_NOOP => '无操作!',
			self::E_IP_DENY => 'IP被拒绝!',
			self::E_DATA_EMPTY => '无数据!',
			self::E_OP_FAIL => '操作失败!',
			self::E_CHECKSUM => '校验错误, 未受权调用!',
			self::E_MAX_SIZE => '文件过大!',
			self::E_INVALID_TYPE => '无效文件类型!',
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

		$this->logfile = 'upload_'.date('Y-m').'.log';
		$this->config = $api_config;
		$this->config['allow'] = strlen($api_config['allow']) ? $api_config['allow'] : '0';
	} /* }}} */

	/**
	 * @fn
	 * @brief api 主入口
	 * 	1. 如果没有提供paths, 根据日期生成路径，根据分秒微秒生成文件，如果文件存在，微秒加1，继续；
	 * 	2. 如果只有提供paths, 根据paths路径，删除文件；
	 * 	3. 如果提供paths和files两参数, 根据paths路径，用files覆盖文件，
	 * 	http://img.com/images/2014/1231/2459598990.jpg
	 * 	http://img.com/images/201412/3124/2459598990.jpg
	 * @access action
	 * @param $args['chksum'] 校检码： username@random@md5(client_key+random)
	 * @param $args['paths'] array. 为更新或删除已经存在的文件时，需要提供paths参数。
	 * @param $args['files'] array. $_FILES/"data:image/gif;base64," 为删除时，count($_FILES)==0.
	 * 	支持$_FILES文件上传 或 经过base64直接编码后的$_POST提交文件。
	 *!@param $args['callback'] 处理结果接收回调程序: 接口post 'ret' json参数，
	 * 	回调程序在页面echo 'ok'代表已经收到回调数据，其他则为通信不正常，
	 * 	此参数为空时不回调.
	 * @return echo json reslut on page.
	 */
	public function index() /* {{{ */
	{
		/* client post data: */
		$args = $_POST;
		$base64 = true;
		/* base64 image? */
		if ( empty($_POST['files']) && !empty($_FILES['files']) ) {
			$base64 = false;
			$args['files'] = $_FILES['files'];
		}
		if ( !count($args) || (empty($args['files']) && empty($args['paths'])) ) {
			return $this->_return($this->_error(self::E_ARGS));
		}
		/* paths & files 要么为空，要么为数组 */
		if ( (!empty($args['paths']) && !is_array($args['paths'])) || 
			(!empty($args['files']) && !is_array($args['files'])) ||	/* base64 */
			(!empty($args['files']['name']) && !is_array($args['files']['name'])) ) { /* _FILES */
			return $this->_return($this->_error(self::E_ARGS));
		}

		$args['referer'] = $_SERVER['HTTP_REFERER'];
		if ( !$this->dochecksum($args) ) {
			wlog($this->logfile, '[checksum error]'.json_encode($args));
			return $this->_return(self::_error(self::E_CHECKSUM));
		}

		/* 3 */
		if ( !empty($args['paths']) &&  !empty($args['files']) ) {
			/* the url path to the local path. */
			$args['paths'] = str_replace(C('IMG_PATH'), C('UPLOAD_PATH'), $args['paths']);
			/* del files */
		}
		/* 2 */
		if ( !empty($args['paths']) &&  empty($args['files']) ) {
			/* TODO? */
			return ;
		}
		/* 1. 建目录，处理上传数据 */
		//if ( empty($args['paths']) &&  !empty($args['files']) ) {}
		$path = C('UPLOAD_PATH').$this->config['dir_rule'];
		if ( !is_dir($path) ) {
			if ( mkdir($path, 0755, true) == false ) {	/* recursive */
				wlog($this->logfile, '[mkdir error]'.$path);
				return $this->_return(self::_error(self::E_SYS_1));
			}
		}

		$_ret = array();
		if ( $base64 ) {
			$_ret = $this->base64_upload($args);
		} else {
			$_ret = $this->files_upload($args);
		}
		$ret = $this->_error(self::E_OK);
		$ret['ret'] = $_ret;
		/* TODO: callback? */
		return $this->_return($ret);
	} /* }}} */

	/**
	 * @brief 上传文件，
	 * @access protected
	 * @param 
	 * @return _ret[]
	 */
	protected function files_upload(&$args = array()) /* {{{ */
	{
		$_ret = array();
		foreach ( $args['files']['tmp_name'] as $i => $v ) {
			/* 检测文件合法性... */
			if ( empty($args['files']['tmp_name'][$i]) ) {
				$_ret[$i] = self::E_DATA_EMPTY.' null';
				continue;
			} else if ( $args['files']['size'][$i] > $this->config['max_size'] ) {
				$_ret[$i] = self::E_MAX_SIZE.' fail, too large.';
				continue;
			}
			$mine_type = $args['files']['type'][$i];
			if ( empty($mine_type) || empty($this->config['allow_type'][$mine_type]) ) {
				$_ret[$i] = self::E_INVALID_TYPE.' fail, unsupported media type '.$mine_type.'.';
				continue;
			}

			/**
			 * 如果没有提供paths, 根据日期生成路径，
			 * 根据分秒微秒生成文件，如果文件存在，微秒加1，继续 
			 */
			if ( empty($args['paths'][$i]) ) {
				$_fid = substr(microtime(), 2, 8);
				$suffix = $this->config['allow_type'][$args['files']['type'][$i]];
				/* 同一微秒被重复的可能性 */
				for ( $_t = 0; $_t < 200; $_t++ ) {
					$_fid = str_pad($_fid, 8, '0', STR_PAD_LEFT);
					$args['paths'][$i] = C('UPLOAD_PATH').$this->config['dir_rule'].date('His').$_fid.$suffix;
					if ( !is_file($args['paths'][$i]) ) {
						break;
					}
					$_fid = (int) $_fid + 1;
				}
			}

			if ( move_uploaded_file($args['files']['tmp_name'][$i], $args['paths'][$i]) ) {
				$_ret[$i] = str_replace(C('UPLOAD_PATH'), C('IMG_PATH'), $args['paths'][$i]);
			} else {
				$_ret[$i] = self::E_SYS_1.' fail, upload error.';
			}
		}
		return $_ret;
	} /* }}} */

	/**
	 * @brief 上传base64编码的文件，
	 * 	data:image/gif;base64,xxx 目前只支持 data:image/xxx格式
	 * @see http://tools.ietf.org/html/rfc2397
	 * @access protected
	 * @param 
	 * @return _ret[]
	 */
	protected function base64_upload(&$args = array()) /* {{{ */
	{
		$_ret = array();
		foreach ( $args['files'] as $i => $v ) {
			if ( empty($v) ) {
				$_ret[$i] = self::E_DATA_EMPTY.' null';
				continue;
			} else if ( substr($v, 0, 5) != 'data:' ) {
				$_ret[$i] = self::E_DATA_INVALID.' fail, data invalid.';
				continue;
			} else if ( strlen($v) > $this->config['max_size'] ) {
				$_ret[$i] = self::E_MAX_SIZE.' fail, too large.';
				continue;
			}
			$boundary = strpos($v, ';base64,');
			$mine_type = substr($v, 5, $boundary - 5);
			$mine_type = substr(strstr($v, ';base64', true), 5);
			if ( $mine_type === false || empty($this->config['allow_type'][$mine_type]) ) {
				$_ret[$i] = self::E_INVALID_TYPE.' fail, unsupported media type '.$mine_type.'.';
				continue;
			}

			/**
			 * 如果没有提供paths, 根据日期生成路径，
			 * 根据分秒微秒生成文件，如果文件存在，微秒加1，继续 
			 */
			if ( empty($args['paths'][$i]) ) {
				$_fid = substr(microtime(), 2, 8);
				$suffix = $this->config['allow_type'][$mine_type];
				/* 同一微秒被重复的可能性 */
				for ( $_t = 0; $_t < 200; $_t++ ) {
					$_fid = str_pad($_fid, 8, '0', STR_PAD_LEFT);
					$args['paths'][$i] = C('UPLOAD_PATH').$this->config['dir_rule'].date('His').$_fid.$suffix;
					if ( !is_file($args['paths'][$i]) ) {
						break;
					}
					$_fid = (int) $_fid + 1;
				}
			}

			/* base64 content */
			$v = substr($v, $boundary + 8);
			if ( file_put_contents($args['paths'][$i], base64_decode($v)) ) {
				$_ret[$i] = str_replace(C('UPLOAD_PATH'), C('IMG_PATH'), $args['paths'][$i]);
			} else {
				$_ret[$i] = self::E_SYS_1.' fail, save error.';
			}
		}
		return $_ret;
	} /* }}} */

	/**
	 * @brief 读取文件接口，同时支持裁剪；
	 * 	路径有的文件，服务器直接返回文件，没有的(裁剪)文件，由此程序检测并生成
	 * 	http://img.com/images/2014/1231/2459598990.200x200.jpg
	 * 	http://img.com/images/201412/3124/2459598990.small.jpg
	 * @access action
	 * @param 
	 * @return 
	 */
	public function rfile() /* {{{ */
	{
		$img_url = parse_url(C(IMG_PATH));
		$img_url['path'] = empty($img_url['path']) ? '/' : $img_url['path'];
		$file = substr($_SERVER['REQUEST_URI'], strlen($img_url['path']));	/* del '/images/' */
		$rfile = C('UPLOAD_PATH').$file;
		$filen = basename($rfile);
		$_tmp = explode('.', $filen);
		/* e.g. 2459598990.200x200.jpg */
		if ( count($_tmp) != 3 ) {
			header('HTTP/1.0 406 Not Acceptable');
			exit;
		}
		$base_file = $_tmp[0].'.'.$_tmp[2];
		$sfile = dirname($rfile).'/'.$base_file;
		//var_dump($file,$rfile,$filen,$_tmp,$sfile);
		if ( !file_exists($sfile) ) {
			header('HTTP/1.0 404 Not Found HeHe~');
			exit;
		}

		/* 缩略图 */
		$w = 150;
		$h = 150;
		/* default small: 150x150 */
		if ( $_tmp[1] != 'small' && strlen($_tmp[1]) > 1 ) {
			$_size = explode('x', $_tmp[1]);
			if ( count($_size) != 2 || !is_numeric($_size[0]) || !is_numeric($_size[1]) ) {
				header('HTTP/1.0 404 Not Found HeHe~~');
				exit;
			}
			$w = (int) $_size[0];
			$h = (int) $_size[1];
		}
		$image = new \Think\Image();
		$image->open($sfile);
		if ( strlen($_tmp[1]) == 1 && is_numeric($_tmp[1]) ) {
			$s_w = $image->width();
			$s_h = $image->height();
			$w = ceil($s_w * $_tmp[1] / 10);
			$h = ceil($s_h * $_tmp[1] / 10);
		}
		$image->thumb($w, $h)->save($rfile);
		header("Content-type: image/gif");
		header("Accept-Ranges: bytes");
		echo file_get_contents($rfile);
		exit;
	} /* }}} */

	/**
	 * @fn
	 * @brief 检测服务器是否在线
	 * @access action
	 * @return 
	 */
	public function ping()  /* {{{ */
	{
		$ret = $this->_error(self::E_OK);
		$ret['ret'] = array('client_ip' => CLIENT_IP, 'time' => date('YmdHis'));
		return $this->_return($ret);
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
	 * @brief checksum
	 * 	远程调用时的鉴权, 如果IP为信任IP, 则直接返回 true.
	 * 	为了提高鉴权校验效率, 一个陌生的IP, 只有第一次请求时需要鉴权, 通过后下次为信任.
	 * 	!!!高效率鉴权只在有正常传送COOKIE的情况有效.
	 * @access protected
	 * @param $args['chksum'] chksum = 用户名@随机数@md5(随机数+密钥);
	 * 	校检 key: md5(client_random+client_key) 
	 * 	client_key = md5(username+'@'+key) client_key 在服务器端生成并保存在客户端，客户端不知校检key
	 * 	客户端需要向服务器索取或申请 username & client_key
	 * 	username 默认可为域名
	 * @see api_config.php $api_config['key']
	 * @return true, success; false, failure.
	 */
	protected function dochecksum(&$args = array())  /* {{{ */
	{
		if ( defined('API_DEBUG') && API_DEBUG ) {
			return true;	/* debug */
		}
		
		$allow = isset($_COOKIE['img_api_ip']) ? $_COOKIE['img_api_ip'] : '';
		/* 上次刚刚校验过, 此次pass */
		if ( !empty($allow) && $allow == md5(getenv('REMOTE_ADDR')) ) {
			return true;
		}
		/* 信任的IP地址, pass */
		if ( $this->config['allow'] == '*' || strpos($this->config['allow'], getenv('REMOTE_ADDR')) !== false ) {
			return true;
		}

		if ( empty($args['chksum']) ) {
			return false;
		}
		$_s = explode('@', $args['chksum']);	/* 0 username, 1 random, 2 client_key */
		if ( count($_s) != 3 ) {
			return false;
		}
		$sum = $_s[0].'@'.$_s[1].'@'.md5($_s[1].md5($_s[0].'@'.$this->config['key']));
		if ( $sum != $args['chksum'] ) {
			return false;
		}

		setcookie('img_api_ip', md5(getenv('REMOTE_ADDR')), 0, '/');
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
