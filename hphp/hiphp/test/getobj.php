<?php
$dirname = dirname(__FILE__);
include($dirname.'/../../hiphp/basics.php');
include($dirname.'/../../hiphp/define.php');
include($dirname.'/../../hiphp/hiObject.php');
include($dirname.'/../../hiphp/controller.php');
include($dirname.'/../../hiphp/appcontroller.php');

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
var_dump(config(null));
/* end file */
