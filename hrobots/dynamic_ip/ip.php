<?php
/**
 * @file collect_ip.php
 * @brief 动态ip查询和设置
 * 	用来处理采集服务器在动态ip网络做服务器时的 客户端查找服务器功能。
 * Copyright (C) 2014 kitsmall.com
 * All rights reserved.
 * 
 * @package hrobots
 * @author Langr <hua@langr.org> 2014/05/22 13:38
 * 
 * $Id: ip.php 75 2014-05-26 03:57:48Z huanghua $
 */

/* 采集服务器的服务器路径 */
define('COLLECT_PATH', '/server/api');
define('SERVER_LOG', 'logs/');

if ( !defined('CLIENT_IP') ) { define('CLIENT_IP', getenv('HTTP_X_FORWARDED_FOR') ? getenv('HTTP_X_FORWARDED_FOR') : getenv('REMOTE_ADDR')); }

/* act: null, beat, serverinfo */
$act = empty($_GET['act']) ? 'index' : $_GET['act'];
/* name: server alias name */
$name = empty($_GET['name']) ? 'server' : $_GET['name'];
$path = empty($_GET['path']) ? '' : $_GET['path'];

/* show CLIENT_IP */
if ( $act == 'index' ) {
	echo CLIENT_IP;
	exit;
}
/* 心跳，设置ip，可为多个服务保存动态ip */
if ( $act == 'beat' ) {
	$name = SERVER_LOG.$name.date('Ym').'.txt';
	if ( empty($path) ) {
		$server = 'http://'.CLIENT_IP.COLLECT_PATH;
	} else {
		$server = $path;
	}
	file_put_contents($name, date('Y-m-d H:i:s @ ').$server."\n", FILE_APPEND | LOCK_EX);
	echo 'ok';
	exit;
}
/* server info */
if ( $act == 'serverinfo' ) {
	$name = SERVER_LOG.$name.date('Ym').'.txt';
	if ( !is_file($name) ) {
		exit;
	}
	$res = file($name);
	$num = count($res);
	$res = explode(' @ ', $res[$num - 1]);
	echo trim($res[1]);
	exit;
}
