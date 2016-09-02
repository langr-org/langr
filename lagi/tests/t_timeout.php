<?php
/* $Id: t_timeout.php 14 2011-12-07 05:48:31Z loghua@gmail.com $ */
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

$host = array('localhost', '127.0.0.1', 'pbx-server', 'svn.langr.org');
/* timeout */
//$host = array('192.168.1.166', 'svn.langr.org');
for ( $i = 0; $i < 1; $i++ ) {
$fd = lagi_connect($host[$i], 5038, 'admin', 'amp111');
//$fd = lagi_connect();
//$fd = lagi_connect('127.0.0.1', 5038);
//$fd = lagi_connect('localhost', 5038);
echo "\n$i,fd:$fd, ".$host[$i%4]." $br";
}
echo "\nfd:$fd $br";
//$re = lagi_action($fd, "action: login\r\nusername: test\r\nsecret: test\r\n\r\n");
//echo "<br/>\n".$re."\n";
//$re = lagi_login($fd, "admin", "admin");
//echo "<br/>\n".$re."\n";
$re = lagi_command("Show Channels", $fd);
echo "\n<br/>1\n";var_dump($re);echo "\n";
/* test timeout */
$re = lagi_action("Action: Command\r\nCommand: Show Channels\r\n", $fd);
echo "<br/>2\n";var_dump($re);echo "\n";
$re = lagi_command("core show hints");
echo "<br/>3\n";var_dump($re);echo "\n";
//$re = lagi_command("Show Channels");
echo "<br/>4\n";var_dump($re);echo "\n";
?>
