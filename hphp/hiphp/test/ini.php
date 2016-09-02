<?php
header("Content-type: text/html; charset=utf-8");
define('DS', '/');
define('ROOT', dirname(dirname(dirname(__FILE__))));
define('APP_DIR', basename(dirname(dirname(__FILE__))));
define('APP_PATH', ROOT . DS . APP_DIR . DS);
define('CORE_PATH', ROOT . DS);
define('WWW_ROOT', dirname(__FILE__) . DS);
define('WEBROOT_DIR', basename(dirname(__FILE__)));
echo "<br/>__FILE__:".__FILE__;
echo "<br/>ROOT:".ROOT;
echo "<br/>APP_DIR:".APP_DIR;
echo "<br/>APP_PATH:".APP_PATH;
echo "<br/>CORE_PATH:".CORE_PATH;
echo "<br/>WWW_ROOT:".WWW_ROOT;
echo "<br/>WEBROOT_DIR:".WEBROOT_DIR;

echo "<br/><br/>SCRIPT_NAME:".$_SERVER['SCRIPT_NAME'];
echo "<br/>SCRIPT_FILENAME:".getenv('SCRIPT_FILENAME');
echo "<br/>include_path:".ini_get('include_path');
ini_set('include_path', "../".PATH_SEPARATOR.ROOT.PATH_SEPARATOR.ini_get('include_path'));
//ini_set('include_path', "../".PATH_SEPARATOR.ini_get('include_path'));
echo "<br/>include_path:".ini_get('include_path');
include("t.php");
require("t.php");

function parse_conf($filename) {
        $file = file($filename);
        foreach ($file as $line) {
                if (preg_match("/^\s*([a-zA-Z_][a-zA-Z0-9_]+)\s*=\s*([a-zA-Z0-9_\.\'\"]*)\s*([;#].*)?/",$line,$matches)) {
                        $conf[ $matches[1] ] = $matches[2];
		}
		var_dump($matches);
        }
        return $conf;
}
?>
