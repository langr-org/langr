<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start(); 
@header("Cache-control: private");			/* 加此句后可支持页面回跳 */
@header("Content-Type: text/html; charset=utf-8");	/* 编码设置与服务器无关 */
require_once("./include/config/main.inc.php"); 
require_once("./lib/application.class.php"); 
?>
