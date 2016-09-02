<?php 
/***
 * 项目入口
 *
 * $HeadURL$
 * $Id: index.php 8 2009-10-20 10:05:34Z langr $
 */

include_once ("./include/php/public.php"); 
#include_once ("./include/php/denyIp.php"); 
#include_once ("./include/php/html.php"); 
$App = & new Application(); 
$App->run();
?>
