<?php
/**
 * svg.php infile outfile
 */

/* server run: xvfb :1 -screen 0 800x600x24 & */
$shell_cmd = "DISPLAY=localhost:1.0 ./svg";
$shell_cmd = "./svg.sh";

/* 接受参数 */
$filename = $_POST['filename'];
$width = $_POST['width'];
$type = $_POST['type'];
$svg = $_POST['svg'];
$suffix = 'png';
switch ($type) {
case ('image/svg+xml') :
	$suffix = 'svg'; break;
case ('image/jpeg') :
	$suffix = 'jpeg'; break;
case ('image/jpg') :
	$suffix = 'jpg'; break;
case ('application/pdf') :
	/*$suffix = 'pdf'; break;*/
default :
	$suffix = 'png';
}

if ( $suffix == 'svg' ) {
	export_file($svg, $filename.date('-YmdHis').'.'.$suffix);
	return ;
}

/* */
//echo stripslashes($_POST['svg']);
$tempn = tempnam("/tmp", "SVG");
$temp = fopen($tempn, "wr");
fwrite($temp, $svg);
fclose($temp);

/**
 * 执行转换
 * 如遇无法执行等问题, 请参考或设置: safe_mode_exec_dir
 */
chdir(dirname(__FILE__));
$cmd = "$shell_cmd $tempn $tempn.$suffix";
$tmp = exec($cmd);

/* 发送kill: killall -I svg */
sleep(1);
exec("killall -I svg");

/* export */
$_f = file_get_contents($tempn.'.'.$suffix);
export_file($_f, $filename.date('-YmdHis').'.'.$suffix);
//unlink($tempn);
//unlink($tempn.'.'.$suffix);

function export_file(& $data = '', $fn = '', $sub = '') /* {{{ */
{
	$fn = empty($fn) ? date('YmdHis') : $fn;
	$fn = empty($sub) ? $fn : $fn.'.'.$sub;
	header("Content-type: application/octet-stream");
	header("Accept-Ranges: bytes");
	header("Accept-Length: ".strlen($data));
	header("Content-Disposition: attachment; filename=".basename($fn));
	echo $data;
	exit;
} /* }}} */
?>
