<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start(); 
@header("Cache-control: private");			/* �Ӵ˾���֧��ҳ����� */
@header("Content-Type: text/html; charset=utf-8");	/* ����������������޹� */
require_once("./include/config/main.inc.php"); 
require_once("./lib/application.class.php"); 
?>
