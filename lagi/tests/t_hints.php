<?php
/* $Id: t_hints.php 19 2011-12-22 01:16:32Z loghua@gmail.com $ */
$br = (php_sapi_name() == "cli")? "":"<br>";

if(!extension_loaded('lagi')) {
	dl('lagi.' . PHP_SHLIB_SUFFIX);
}
$module = 'lagi';
$functions = get_extension_funcs($module);
echo "Functions available in the test extension:$br\n";
foreach($functions as $func) {
    echo $func."$br\n";
}
echo "$br\n";

include('inc.class.php');
/* 点击拔号 */
//$host = array('localhost', '127.0.0.1', 'pbx-server', 'svn.langr.org');
$host = array('pbx-server');
$host = array('192.168.1.226');
for ( $i = 0; $i < 1; $i++ ) {
	$fd = lagi_connect($host[$i], 5038, 'admin', 'amp111');
	//$fd = lagi_connect();
	if ( $fd ) {
		$ret = 'ok';
	} else {
		$ret = 'false';
	}
	echo "\n$i $ret,fd:$fd, ".$host[$i]." $br";
}
echo "\nfd:$fd $br";
//$re = lagi_action($fd, "action: login\r\nusername: test\r\nsecret: test\r\n\r\n");
$re = getHints();
echo "\n<br/>1.0\n";var_dump($re);echo "\n";
/*
$re = lagi_command("core show hints", $fd);
echo "\n<br/>1\n";var_dump($re);echo "\n";
$re = lagi_action("Action: Command\r\nCommand: core show channels\r\n\r\n");
echo "<br/>2\n";var_dump($re);echo "\n";
*/
$re = lagi_hints();
echo "<br/>3.0\n";var_dump($re);echo "\n";
$re_1 = lagi_hints('805');
echo "<br/>3.1\n";var_dump($re_1);echo "\n";
$re_1 = lagi_hints('SIP/604');
echo "<br/>3.2\n";var_dump($re_1);echo "\n";
$re_1 = lagi_hints('604');
echo "<br/>3.2\n";var_dump($re_1);echo "\n";
//$re = lagi_command("show channels");
//echo "<br/>3.2\n";var_dump($re);echo "\n";
//$re = lagi_channel_analyse('805', $re['data']);
//echo "<br/>3.3\n";var_dump($re);echo "\n";
//$re = lagi_outcall('SIP/808', "808", '805');
/*
echo "<br/>3\n";var_dump($re);echo "\n";
$re = lagi_command("database show DND 810");
echo "<br/>4\n";var_dump($re);echo "\n";
$re = lagi_command("database show DND/810");
echo "<br/>4\n";var_dump($re);echo "\n";
*/
?>
