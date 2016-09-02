<?php
/** 
 * @file define.php
 * @brief 
 * 
 * Copyright (C) 2011 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package hiphp
 * @author Langr <hua@langr.org> 2011/11/13 23:37
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: define.php 20 2011-12-17 01:37:12Z loghua@gmail.com $
 */
if ( c('DEBUG') ) {
	echo __FILE__.'<br/>';
}
define(PS, ';');
echo dirname(__FILE__).'<br/>';
echo basename(__FILE__).'<br/>';
echo get_include_path().'<br/>';
/* 定义客户端 IP */
$rcip = getenv('HTTP_X_FORWARDED_FOR');
$cip = $rcip ? $rcip : getenv('REMOTE_ADDR');

define('CLIENT_IP', $cip);

$rewrite_url = array('m'=>'index', 'a'=>'index', 'parms'=>array('p'=>'2'));

config('APP_LOG_PATH', dirname(__FILE__));		/* */
config('CLIENT_IP', $cip);				/* */
config('CFG_APP_PATH', '');			/* 架构相对 Web 文档根目录绝对路径  */
config('CFG_DEFAULT_MODULE', 'index');			/* 缺省类 */
config('CFG_DEFAULT_ACTION', 'index');			/* 缺省操作 */

config('cache.path', 'cache/');				/* cache */
config('cache.timeout', 36000);				/* cache */
config('session.type', 'memcache');			/*  */
config('session.path', c('cache.path').'session/');	/*  */
/* 语言编码设置 */
config('cfg.web.charset', 'UTF-8');			/* 网站语言设置(UTF-8 编码)，可选值：GB2312、BIG5 */
/* 模板项目相关配置 */
config('cfg.tmpl.path', 'tmpl');			/* 模板目录 */

config('cfg.tmpl.lang', 'UTF-8');			/* 程序及模板的语言方式 */
config('cache.html.path', 'cache/html/');			/* cache */

/* ReWrite 支持 */
config('REWRITE', 0);
config('REWRITE_ROUTE', $rewrite_url);

/* end file */
