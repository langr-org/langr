<?php
/**
 * @file index.php
 * @brief 
 * 
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package client
 * @author Langr <hua@langr.org> 2014/05/13 16:20
 * 
 * $Id: index.php 64 2014-05-24 05:54:31Z huanghua $
 */

if ( !defined('APP_PATH') ) { define('APP_PATH', dirname(__FILE__).'/'); }
include_once(APP_PATH.'lib/lib.php');
if ( substr(PHP_OS, 0, 3) == 'WIN' ) {
	include_once(APP_PATH.'lib/cpuinfo.php');
	include_once('lib/client.gbk.php');
} else {
	include_once(APP_PATH.'lib/client.php');
}

if ( defined('APP_DEBUG') && !APP_DEBUG ) {
	error_reporting(0);
}

/* cli argv[1] module */
if ( PHP_SAPI == 'cli' && !empty($argv[1]) ) {
	$_GET['module'] = $argv[1];
}

/**
 * @fn 入口
 * @brief 
 * @param 
 * @return 
 */
function _main() /* {{{ */
{
	$c = new client();
	/* run超过一天时，后自动退出，所以这里会有个循环 */
	while ( true ) {
		$c->run();
	}
} /* }}} */

_main();
/* end */
