<?php
/** 
 * @file basics.php
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package hphp
 * @author Langr <hua@langr.org> 2011/11/13 20:28
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: basics.php 21 2012-05-17 10:47:34Z loghua@gmail.com $
 */
/** 
 * @file controller.php
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package test
 * @author Huang Hua <hua@langr.org> 2011/11/02 15:58
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: basics.php 21 2012-05-17 10:47:34Z loghua@gmail.com $
 */
/* debug? */
$__inc_files[] = __FILE__;

config('DEBUG', true);

/**
 * @fn
 * @brief 日志记录函数
 * @param 
 * @return 
 */
function wlog($log_file, $log_str, $log_size = 4194304) /* {{{ */
{
	ignore_user_abort(TRUE);

	if ( empty($log_file) ) {
		$log_file = 'log_file';
	}
	if ( defined('APP_LOG_PATH') /*&& substr($log_file, 0, 1) != '/'*/) {
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
		$log_str = '['.date('Y-m-d H:i:s').'] '.$cip.$log_str."\r\n";
		fwrite($fp, $log_str);
		flock($fp, LOCK_UN);
	}
	fclose($fp);

	ignore_user_abort(FALSE);
} /* }}} */

/**
 * @fn
 * @brief 配置读写函数
 * @param 
 * @return 
 */
function config($key, $value = null) /* {{{ */
{
	static $__hi__config;

	if ( $key === null ) {
		return $__hi__config;
	}
	if ( $value === null ) {
		return isset($__hi__config[$key]) ? $__hi__config[$key] : false;
	} else {
		$__hi__config[$key] = $value;
		return true;
	}
} /* }}} */

/**
 * @fn
 * @brief 缩写的配置读写函数
 * @param 
 * @return 
 */
function c($key, $value = null) /* {{{ */
{
	return config($key, $value);
} /* }}} */

/**
 * @fn
 * @brief 装载(应用层)插件或工具
 * @param 
 * @return 
 */
function load($name, $path = null) /* {{{ */
{
	return include($path.$name.'.php');
} /* }}} */

/**
 * @fn
 * @brief 装载(框架层)库文件或 core libs
 * @param 
 * @return 
 */
function uses($name, $path = null) /* {{{ */
{
	return include($path.$name.'.php');
} /* }}} */

/**
 * @fn
 * @brief 本地化函数.
 *
 * 如果有翻译多语言, 则(根据浏览器或指定语言)返回本地化语言.
 * @see lang::tr()
 * @param 
 * @return 
 */
function tr($name) /* {{{ */
{
	return $name;
} /* }}} */

/* end file */
