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
 * $Id: define.php 21 2012-05-17 10:47:34Z loghua@gmail.com $
 */
$__inc_files[] = __FILE__;

/* 定义客户端 IP */
$rcip = getenv('HTTP_X_FORWARDED_FOR');
$cip = $rcip ? $rcip : getenv('REMOTE_ADDR');

define('CLIENT_IP', $cip);

$rewrite_url = array('m'=>'index', 'a'=>'index', 'parms'=>array('p'=>'2'));

/* app/config/config.php */
config('APP_LOG_PATH', dirname(__FILE__));		/* */
config('APP_WEB_PATH', dirname($_SERVER['SCRIPT_NAME']));	/* 入口脚本相对 Web 文档根目录绝对路径  */
config('CFG_APP_PATH', '');				/* 架构相对 Web 文档根目录绝对路径  */
config('CFG_DEFAULT_MODULE', 'index');			/* 缺省类 */
config('CFG_DEFAULT_ACTION', 'index');			/* 缺省操作 */

/* 模板项目相关配置 */
config('CFG_TEMPLATE_PATH', 'tmpl');			/* 模板目录 */

/* 语言编码设置 */
config('CFG_CHAR_SET', 'UTF-8');			/* 网站语言设置(UTF-8 编码)，可选值：GB2312、BIG5 */
config('CFG_TEMP_LANG', 'UTF-8');			/* 程序及模板的语言方式 */

/* ReWrite 支持 */
config('url.rewrite', 0);				/* rewrite: 0, 1 HTTP rewrite 模式, 2 PATH_INFO 模式 */
config('url.suffix', '.html');
config('url.path', dirname($_SERVER['SCRIPT_NAME']));	/* 入口脚本相对 Web 文档根目录绝对路径 */
config('url.route', array());

/* Router 配置 app/config/routes.php */
router('/', array('ctrl'=>'index', 'act'=>'index'));
router('/blogs/*', array('ctrl'=>'blog'));		/* '*'优先匹配act; 找不到匹配act则匹配默认act, '*'则作为默认act参数 */
router('/blog/*', array('ctrl'=>'blog', 'act'=>'content'));
router('/news/*', array('ctrl'=>'news', 'act'=>'news', 'params'=>array('show'=>'5')));

/* end file */
