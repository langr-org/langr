<?php
define('API_DEBUG', 'debug');

define('JS_PATH', './');

$args = array();
$args = isset($_POST['act']) ? $_POST : $_GET;
if ( !$args['act'] ) {
	//$args['act'] = 'confadd';
	$args['act'] = 'index';
}
if ( function_exists($args['act']) ) {
	if ( empty($args['ffn']) ) {
		$args['ffn'] = 'popwindow.conf.'.$args['fn'].'.js';
	}
	$args['act']($args);
}

/* */
function index($args)
{
	display('body');
}

/* 取配置文件列表 */
function conflist($args)
{
	$files_ok = array();
	$d = dir(JS_PATH);
	while ( 1 ) {
		$tmp = $d->read();
		if ( false === $tmp ) {
			break;
		}
		if ( substr($tmp, 0, 15) == 'popwindow.conf.' && substr($tmp, -3) == '.js' && substr($tmp, 15, -3) ) {
			$files_ok[substr($tmp, 15, -3)] = $tmp;
		}
	}
	//var_dump($files_ok);
	$tmpl['list'] = $files_ok;
	display('list', $tmpl);
}

/* 编辑/新建配置文件 */
/**
 * args['fn'] 
 */
function confadd($args)
{
	$tmpl['do_edit'] = '新增';
	$tmpl['fn'] = 'conf'.date('ymdHi');
	$tmpl['pop_title'] = "{'from':'来电:','to':'去电:','inv':'内线:'}";
	display('edit', $tmpl);
}

function confedit($args)
{
	$tmpl = array();
	$tmpl = _confread($args['ffn']);
	$tmpl['fn'] = $args['fn'];
	$tmpl['do_edit'] = '编辑';

	$tmpl['pop_show_'.$tmpl['pop_show']] = 'selected';
	$tmpl['pop_show_scroll_'.$tmpl['pop_show_scroll']] = 'selected';
	$tmpl['pop_show_title_'.$tmpl['pop_show_title']] = 'selected';
	$tmpl['pop_flash_title_'.$tmpl['pop_flash_title']] = 'selected';
	$tmpl['pop_connect_'.$tmpl['pop_connect']] = 'selected';

	display('edit', $tmpl);
}

/**
 * !@param args['do'] 'edit', 'add'
 * @param args['filename'] 
 * @param args['pop_url'] 
 * @param args['pop_XXX'] 
 */
function confsave($args)
{
	//var_dump($args);exit;
	$line = "/**\r\n * {$args['ffn']}\r\n * @auto ".date('Y-m-d H:i:s')."\r\n */\r\n\r\n";
	foreach ( $args as $var => $value ) {
		$sub = substr($var, 0, 4);
		if ( $sub != 'pop_' && $sub != 'api_' ) {
			continue;
		}
		/* 首尾有任意引号或{}[]时不再加引号 */
		if ( $value == 'true' || $value == 'false' ||
				($value[0] == "'" || $value[strlen($value) - 1] == "'") || 
				($value[0] == "\"" || $value[strlen($value) - 1] == "\"") ||
				($value[0] == "[" && $value[strlen($value) - 1] == "]") ||
				($value[0] == "{" && $value[strlen($value) - 1] == "}") ) {
			$line .= 'var '.$var." = $value;\r\n";
		} else {
			$line .= 'var '.$var." = \"$value\";\r\n";
		}
	}
	if ( empty($args['ffn']) ) {
		return $line;
	}
	$fp = fopen(JS_PATH.$args['ffn'], 'w');
	fwrite($fp, $line);
	fclose($fp);

	/*
	display('edit');
	header('Location: ?act=confedit&fn='.$args['fn']);
	*/
	jump("?", "配置保存成功", 'top');
	return ;
}

function _confread($ffn)
{
	if ( !file_exists(JS_PATH.$ffn) ) {
		return array();
	}

	$res = array();
	$tmp = file(JS_PATH.$ffn);
	foreach ( $tmp as $line ) {
		if ( substr($line, 0, 3) != 'var' ) {
			continue;
		}
		$s = strpos($line, '=');
		$v = trim(substr($line, $s + 1));
		$v = substr($v, 0, -1);
		/* 有对称引号时去掉引号 */
		if ( ($v[0] == "'" && $v[strlen($v) - 1] == "'") || ($v[0] == "\"" && $v[strlen($v) - 1] == "\"") ) {
			$v = substr($v, 1, -1);
		}
		$res[trim(substr($line, 4, $s - 4))] = $v;
	}

	return $res;
}

/* 删除配置文件 */
function confdel($args)
{
	unlink(JS_PATH.$args['ffn']);
	//display('edit');
	jump("?", "配置删除成功", 'top');
}

/* tmpl */
function tmpl($tmpl_file, $tmpl = array())
{
	$d = & $tmpl;
	$__tmpf = JS_PATH.'tmpl/'.$tmpl_file.'.tmpl.php';
	if ( !file_exists($__tmpf) ) {
		if ( !file_exists($tmpl_file) ) {
			$tmplContent = "tmpl not exists: $tmpl_file";
		} else {
			$tmplContent = file_get_contents($tmpl_file);
		}
		/* 只支持模板变量
		$tmplContent = str_replace("\"", "\\\"", $tmplContent);
		$temp	= preg_replace('/(\{\$)(.+?)(\})/is', '".'.'$d[\'\\2\']'.'."', $tmplContent);
		eval("\$temp = \"$temp\";");
		$temp = str_replace("\\\"", "\"", $temp);
		$temp  = StripSlashes($temp);
		*/
		/* 支持 php */
		$tmplContent = preg_replace('/(\{\$)(.+?)(\})/is', '<'.'?=$d[\'\\2\']?'.'>', $tmplContent); 
		$tmplContent = preg_replace('/(charset=)(.+?)(\")/is', 'charset=UTF-8"', $tmplContent); 
		$tmplContent = str_replace('#?#','?',$tmplContent);
		file_put_contents($__tmpf, $tmplContent);
	}
	include($__tmpf);
		
	return $temp;
}

function display($tmpl_file, $tmpl = array())
{
	$tmpl_file = 'conf.'.$tmpl_file.'.html';
	tmpl($tmpl_file, $tmpl);
	//eval(tmpl($tmpl_path, $tmpl));
	return ;
}

function jump($url, $msg, $location = '')
{
	$alert = '';
	if ( $msg ) {
		$alert = "alert('$msg')";
	}
	if ( $location ) {
		$location = ".$location";
	}
	echo "<script>$alert;window$location.location.href='$url';</script>";
	exit;
}
