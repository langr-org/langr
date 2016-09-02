<?php
/** 
 * @file index.php
 * @brief 
 * 
 * Copyright (C) 2012 LangR.Org
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @package web
 * @author Langr <hua@langr.org> 2012/05/16 17:57
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 * $Id: index.php 21 2012-05-17 10:47:34Z loghua@gmail.com $
 */
$__inc_files[] = __FILE__;

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(dirname(__FILE__))));
define('APP_DIR', basename(dirname(dirname(__FILE__))));
define('WEBROOT_DIR', 'web');
define('WWW_PATH', ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS);

/*
if ( !defined('CORE_PATH' )) {
	if ( function_exists('ini_set') && ini_set('include_path', CORE_INCLUDE_PATH.PATH_SEPARATOR.ROOT.DS.APP_DIR.DS.PATH_SEPARATOR.ini_get('include_path')) ) {
		define('APP_PATH', null);
		define('CORE_PATH', null);
	} else {
		define('APP_PATH', ROOT.DS.APP_DIR.DS);
		define('CORE_PATH', CORE_INCLUDE_PATH.DS);
	}
}
*/
/* NOTE: 如果将 app 移到 hiphp 以外的(非同一个)目录, 请重新设置 CORE_APTH 到相应路径 */
define('CORE_PATH', ROOT.DS.'hiphp'.DS);
define('APP_PATH', ROOT.DS.APP_DIR.DS);

$dirname = dirname(__FILE__);
include($dirname.'/../../hiphp/basics.php');
include($dirname.'/../../hiphp/router.php');
include($dirname.'/../../hiphp/define.php');
include($dirname.'/../../hiphp/hiObject.php');
include($dirname.'/../../hiphp/dispatcher.php');
include($dirname.'/../../hiphp/controller.php');
include($dirname.'/../../hiphp/appcontroller.php');

echo "<br/>SCRIPT_NAME:".$_SERVER['SCRIPT_NAME'];
echo "<br/>dirname(SCRIPT_NAME):".dirname($_SERVER['SCRIPT_NAME'])."<br/>";
var_dump($_GET);
$o0 = controller::test();
$oa = controller::getInstance();
echo '<br/>oa<br/>'; var_dump($oa);
$o0 = controller::getInstance('controller');
echo '<br/>o0<br/>'; var_dump($o0);

$o1 = hiObject::getInstance();
echo '<br/>o1<br/>'; var_dump($o1);
$o2 = hiObject::getInstance();
echo '<br/>o2<br/>'; var_dump($o2);
$o3 = hiObject::getInstance();
echo '<br/>o3<br/>'; var_dump($o3);

$o4 = new hiObject();
echo '<br/>o4<br/>'; var_dump($o4);
$o5 = new hiObject();
echo '<br/>o5<br/>'; var_dump($o5);

$o6 = hiObject::getInstance();
echo '<br/>o6<br/>'; var_dump($o6);

$o0 = hiObject::test();
echo "<pre>"; var_dump(config(null)); echo "</pre>";

echo "<pre>"; var_dump($__inc_files); echo "</pre>";
/* end file */
