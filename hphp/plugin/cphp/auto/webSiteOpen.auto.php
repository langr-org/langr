#!/usr/local/php4/bin/php
<?PHP
/**
 * 自动打开网站开始运行
 *
 * @author Arnold 2007/04/25
 * @package Core
 */
include_once('../include/config/system.inc.php');
if (is_array($systemSetup)) {
	$systemSetup['active']['value'] = 'True';
	$systemSetupContent = var_export($systemSetup,True);
	$systemSetupContent = "<"."?\r\n\$systemSetup = ".$systemSetupContent.";\r\n?".">";
	$fp=fopen('../include/config/system.inc.php',"w");
	flock($fp, LOCK_EX);
	fwrite($fp,$systemSetupContent);
	flock($fp, LOCK_UN);
	fclose($fp);
}
?>