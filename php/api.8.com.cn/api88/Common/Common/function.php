<?php
/**
 * @file function.php
 * @brief 
 * 
 * Copyright (C) 2016 ZKC.com
 * All rights reserved.
 * 
 * @package Common
 * @author Langr <hua@langr.org> 2016/05/11 16:03
 * 
 * $Id: function.php 62324 2016-06-20 08:20:50Z huanghua $
 */

/**
 * 获取月份和日期  
 * 
 * @param int $time 时间戳 
 * @access public
 * @return array 
 */
function get_month_date($time) /* {{{ */
{
	return array('m'=>date('m',$time),'d'=>date('d',$time));
} /* }}} */

/** 
 * 加密手机,金额等 
 * 只作兼容旧版本用
 */ 
function _dfzip($num) /* {{{ */
{ 
	return base64_encode(base64_encode(base64_encode($num)));
} /* }}} */

/** 
 * 解密手机,金额等 
 * 只作兼容旧版本用
 */ 
function _undfzip($num) /* {{{ */
{ 
	return base64_decode(base64_decode(base64_decode($num)));
} /* }}} */

/**
 * @fn
 * @brief 日志记录函数
 * @param $log_file	日志文件名
 * @param $log_str	日志内容
 * @param $show		日志内容是否show出
 * @param $log_size	日志文件最大大小，默认20M
 * @return void
 */
function wlog($log_file, $log_str, $show = false, $log_size = 20971520) /* {{{ */
{
	ignore_user_abort(TRUE);

	$time = '['.date('Y-m-d H:i:s').'] ';
	if ( $show ) {
		echo $time.$log_str.((PHP_SAPI == "cli") ? "\r\n" : "<br>\r\n");
	}
	if ( empty($log_file) ) {
		$log_file = 'wlog.txt';
	}
	if ( defined('APP_LOG_PATH') ) {
		$log_file = APP_LOG_PATH.$log_file;
	}

	if ( !file_exists($log_file) ) { 
		$fp = fopen($log_file, 'a');
	} else if ( filesize($log_file) > $log_size ) {
		$fp = fopen($log_file, 'w');
	} else {
		$fp = fopen($log_file, 'a');
	}

	if ( flock($fp, LOCK_EX) ) {
		$cip	= defined('CLIENT_IP') ? '['.CLIENT_IP.'] ' : '['.getenv('REMOTE_ADDR').'] ';
		$log_str = $time.$cip.$log_str."\r\n";
		fwrite($fp, $log_str);
		flock($fp, LOCK_UN);
	}
	fclose($fp);

	ignore_user_abort(FALSE);
} /* }}} */

/**
 * @fn
 * @brief 通过http协议模拟浏览器向服务器请求数据
 * 	此函数有超级牛力^^
 * 	支持并独立保存COOKIE, 支持HTTP/1.1 Transfer-Encoding: chunked.
 * 	向 $url 以 POST 方式传递数据, 支持 ssl 和 http 方式, 支持 http 帐号验证.
 * 	TODO: 将 cookie 保存为 json 格式, 或者以行记录.
 * 	NOTE: 如果服务器端 Accept-Encoding 默认不为 identity, 
 * 		请发送 Accept-Encoding 头, 确保返回的消息不被编码:
 * 		"Accept-Encoding: " or "Accept-Encoding: identity"
 * @author Langr<hua@langr.org>
 * @param $url		
 * @param $data		POST 到对方的数据, 为空时以 GET 方式传递, e.g. array('n1'=>'v1','n2'=>'v2') or "n1=v1&n2=v2"
 * @param $header	http header 头数据, e.g. array('User-Agent'=>'Mozilla/5.0') or "User-Agent: Mozilla/5.0\r\n"
 * @param $cookie_path	cookie存储的路径, 为 'nonsupport' 时则不存储, 
 * 			如果定义了 'H2H_COOKIE_PATH' 常量, 则不关注 'nonsupport' 值, 全部存储.
 * @param $debug	调试: false 不调试, true 调试, 记录发送接收头到日志文件.
 * 			如果定义了 'APP_DEBUG' 常量, 则不关注此参数.
 * @return $url 传回的web数据
 */
function http2host($url, $data = array(), $header = "User-Agent: Mozilla/5.0 (Windows NT 6.1) Chrome/33.0\r\n", $cookie_path = 'nonsupport', $debug = false) { /* {{{ */
	$encoded = '';
	$post = 'POST';
	$line = '';

	if ( defined('APP_DEBUG') ) {
		$debug = APP_DEBUG;
	}
	/* 准备数据 */
	if ( is_array($data) && count($data) > 0 ) {
		while ( list($k, $v) = each($data) ) {
			$encoded .= rawurlencode($k)."=".rawurlencode($v)."&";
		}
		$encoded = substr($encoded, 0, -1);
	} else if ( is_string($data) ) {
		$encoded = $data;
	} else {
		$post	= 'GET';
	}

	$urls = parse_url($url);
	if ( !isset($urls['port']) ) { $urls['port'] = 80; }
	if ( !isset($urls['query']) ) { $urls['query'] = ''; }
	if ( !isset($urls['path']) ) { $urls['path'] = '/'; }
	if ( !isset($urls['host']) ) { return '-11 url error'; }

	$m = '';
	if ( $urls['scheme'] == 'https' ) {
		$m = 'ssl://';
		$urls['port'] = ($urls['port'] == 80) ? 443 : $urls['port'];
	}
	if ( ($urls['scheme'] == 'ssl' || $urls['scheme'] == 'udp') ) {
		$m = $urls['scheme'].'://';
	}
	$fp = @fsockopen($m.$urls['host'], $urls['port']);
	if ( !$fp ) {
		return "-12 failed to open socket to {$urls['host']}:{$urls['port']}";
	}

	/* request */
	$request_headers = '';
	//$request_headers .= sprintf($post." %s%s%s HTTP/1.1\r\n", $urls['path'], $urls['query'] ? '?' : '', $urls['query']);
	$request_headers .= sprintf($post." %s%s%s HTTP/1.0\r\n", $urls['path'], $urls['query'] ? '?' : '', $urls['query']);
	$request_headers .= "Host: {$urls['host']}\r\n";
	/* basic 认证 */
	if ( !empty($urls['user']) ) {
		$request_headers .= "Authorization: Basic ".base64_encode($urls['user'].':'.$urls['pass'])."\r\n";
	}
	if ( $post == 'POST' ) {
		$request_headers .= "Content-type: application/x-www-form-urlencoded; charset=utf-8\r\n";
		$request_headers .= "Content-length: ".strlen($encoded)."\r\n";
	}
	/* 自定义 header */
	if ( is_array($header) && count($header) > 0 ) {
		while ( list($k, $v) = each($header) ) {
			$request_headers .= "$k: $v\r\n";
		}
	} else if ( is_string($header) ) {
		$request_headers .= $header;
	}

	/* COOKIE 支持, send */
	$_allow_cookie = true;
	if ( defined('H2H_COOKIE_PATH') ) {
		$cookie_path = H2H_COOKIE_PATH;
	} else if ( $cookie_path == 'nonsupport' ) {
		$_allow_cookie = false;
		$cookie_path = '';
	}
	$cookie_file = $cookie_path.$urls['host'].'.cookie';
	if ( $_allow_cookie && file_exists($cookie_file) ) {
		/* TODO: json */
		$request_headers .= "Cookie: ".trim(file_get_contents($cookie_file))."\r\n";
	}
	$request_headers .= "Connection: close\r\n\r\n";

	if ( $post == 'POST' ) {
		$request_headers .= "$encoded\r\n";
	}
	if ( $debug ) {
		wlog('http2host.log', "Host[$m{$urls['host']}:{$urls['port']}] URL[$url]\r\n".$request_headers);
	}
	fputs($fp, $request_headers);

	/* response */
	$response_headers = '';
	$line = fgets($fp, 4096);
	$response_headers .= $line;
	/* http error? 3xx 不处理 */
	if ( !preg_match("/^HTTP\/1\.. 200/", $line) ) {
		$_errno = substr(trim($line), 9);
		if ( $_errno[0] != '3' ) {
			return '-'.$_errno;
		}
	}

	$results = '';
	$inheader = true;
	$i = 0;
	$cookie_o = array();
	$cookie_n = array();
	$has_chunk = false;	/* 数据分块的？ */
	while ( !feof($fp) ) {
		$line = fgets($fp, 4096);
		if ( $line === false ) {
			break;
		}
		if ( $inheader ) {
			$response_headers .= $line;
		}
		if ( $inheader && substr($line, 0, 19) == 'Transfer-Encoding: ' ) {
			if ( trim(substr($line, 19)) == 'chunked' ) {
				$has_chunk = true;
			}
		}
		/* COOKIE 支持, recv */
		if ( $inheader && $_allow_cookie && substr($line, 0, 12) == 'Set-Cookie: ' ) {
			$line = substr(trim($line), 12);
			$cookie_o = $cookie_n = array();
			/* TODO: json */
			if ( file_exists($cookie_file) ) {
				$cookie_old = trim(file_get_contents($cookie_file));
				$cookie_array = explode('; ', $cookie_old);
				foreach ( $cookie_array as $k=>$v ) {
					$eq = strpos($v, '=');
					if ( $eq === false ) continue;
					$cookie_o[substr($v, 0, $eq)] = substr($v, $eq + 1);
				}
			}
			$cookie_new = explode('; ', $line);
			$eq = strpos($cookie_new[0], '=');
			$cookie_n[substr($cookie_new[0], 0, $eq)] = substr($cookie_new[0], $eq + 1);

			$cookie_n = array_merge($cookie_o, $cookie_n);
			$line = '';
			foreach ( $cookie_n as $k=>$v ) {
				$line .= $k.'='.$v.'; ';
			}
			$line = substr($line, 0, -2);
			file_put_contents($cookie_file, $line);
		}
		/* 去掉第一次的空行 */
		if ( $inheader && ($line == "\n" || $line == "\r\n") ) {
			$inheader = false;
			break;
		}
	}
	/* line 1 */
    	$_data = fgets($fp, 4096);
	$r = trim($_data);
	$rn = 0;		/* 读块长度 */
	/* HTTP/1.1 Transfer-Encoding: chunked 支持，正文中的块长度标识 */
	if ( $has_chunk && is_numeric('0x'.$r) ) {
		$rn = base_convert($r, 16, 10);
		wlog('http2host.log', "length chunk:$r,$rn,total:".strlen($results));
		$_data = fgets($fp, 4096);	/* has_chunk 去掉第一行 \r or \r\n */
		if ( $_data != "\n" && $_data != "\r\n" ) {
			$results .= $_data;
			$rn -= strlen($_data);
		}
	} else {
    		$results .= $_data;
	}
	do {
		/* 读块 */
		if ( $has_chunk && $rn > 0 ) {
			$__tmp = '';
			while ( ($__tmp = fread($fp, $rn)) !== false ) {
				$rn = $rn - strlen($__tmp);
    				$results .= $__tmp;
				if ( $rn == 0 ) { break; }
			}
		}
    		$_data = fgets($fp, 4096);
    		if ( $_data === false ) {
        		break;
		}
		/* 取块长度 */
		if ( $has_chunk ) {
			if ( $_data != "\n" && $_data != "\r\n" ) {
				$rn = base_convert(trim($_data), 16, 10);
				wlog('http2host.log', "length chunk:".trim($_data).",$rn,total:".strlen($results));
			}
			continue;
		}
		$results .= $_data;
	} while ( !feof($fp) );
	fclose($fp);
	if ( $debug ) {
		wlog('http2host.log', "\r\n".$response_headers);
	}

	return $results;
} /* }}} */

/**
 * @fn
 * @brief 数组转换为xml
 * @param 
 * @return 
 */
function array2xml(&$data = array()) /* {{{ */
{
	$xml = "<?xml version='1.0' encoding='UTF-8' ?>";
	return $xml._array2xml($data);
} /* }}} */

/**
 * @brief 数组转换为xml
 */
function _array2xml(&$data = array()) /* {{{ */
{
	$xml = '';
	foreach ( $data as $key => $val ) {
		if ( is_array($val) ) {
			$xml .= "<$key>"._array2xml($val)."</$key>";
		} else {
			$xml .= "<$key>$val</$key>";
		}
	}
	return $xml;
} /* }}} */

/* end file */
