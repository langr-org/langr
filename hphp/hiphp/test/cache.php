<?php
session_start();

/**
 * array to cache写入函数
 * 可 cache 文本，数组，数组->变量，数组->define
 * $caches: string or array. string -> type 默认 text, array -> type 默认 array
 * $type = 'text', 'array', 'var', 'define';
 */
function acache($script, &$caches, $rootpath = '.', $prefix = 'cache_', $type = 'array') {
	$cachedata = '';
	if ( is_array($caches) ) {
		//foreach ( $caches as $name ) {
			$cachedata .= putcachevars($caches, $type);
			//$cachedata .= arraytostr($caches);
		//}
	} else {
		$cachedata .= '/* string '.date('Y-m-d H:i:s').' */'.$caches;
	}

	$dir = $rootpath;
	if ( !is_dir($dir) ) {
		mkdir($dir, 0777);
	}
	if ( $fp = fopen("$dir/$prefix$script", 'w') ) {
		fwrite($fp, "<?php\n//CACHE! cache file, DO NOT modify me!\n".
			"//Created on ".date("Y-m-d H:i:s")."\n\n$cachedata?>");
		fclose($fp);
	} else {
		wlog("Can not write to cache files, please check directory $dir/ .");
	}
}

/**
 * 字符串变量，数组变量 处理函数
 * $type = 'array', 'var', 'define';
 */
function putcachevars(&$data, $type = 'array') {
	$evaluate = '';
	//if ( is_array($data) ) {}
	if ( $type == 'array' ) {
		$evaluate .= '$'.get_var_name($data).' = '.arraytostr($data).";\n";
	} else {
		foreach ( $data as $key => $val ) {
			$val = addcslashes($val, '\'\\');
			$evaluate .= $type == 'var' ? "\$$key = '$val';\n" : "define('".strtoupper($key)."', '$val');\n";
		}
	}
	return $evaluate;
}

/**
 * 数组到字符串处理函数
 */
function arraytostr(&$array, $level = 0) {
	$space = '';
	for ( $i = 0; $i <= $level; $i++ ) {
		$space .= "\t";
	}
	$evaluate = "Array\n$space(\n";
	$comma = $space;
	foreach ( $array as $key => $val ) {
		$key = is_string($key) ? '\''.addcslashes($key, '\'\\').'\'' : $key;
		$val = !is_array($val) && (!preg_match("/^\-?\d+$/", $val) || strlen($val) > 12) ? '\''.addcslashes($val, '\'\\').'\'' : $val;
		if ( is_array($val) ) {
			$evaluate .= "$comma$key => ".arraytostr($val, $level + 1);
		} else {
			$evaluate .= "$comma$key => $val";
		}
		$comma = ",\n$space";
	}
	$evaluate .= "\n$space)";
	return $evaluate;
}

/**
 * 获取变量的变量名
 * 没必要用就少用.
 */
function get_var_name(&$src) {
	/* 存储当前变量值 */
	$save = $src;
	/* 改变当前变量$src的值为一个不太会与其他变量重复的值 */
	$src = '__hphp__var__'.time();
	/* 存储所有变量值 */
	$g_var = $GLOBALS;
	/* 在函数中不要直接遍历$GLOBALS,会出现堆栈问题 */
	foreach ( $g_var  as $k => $v ) {
		if ( $src == $v ) {
			/* 还原变量值 */
			$src = $save;
			return $k;
		}
	}
	$src = $save;
	return ;
}

/**
 * 记录日志
 * $log_file 日志文件的路径及档案名
 * $log_str 日志内容, $log_size 日志文件的最大Size, 默认为 4M (4194304字节)
 */
function wlog($log_file, $log_str, $log_size = 4194304) 
{
	ignore_user_abort(TRUE);

	if ( empty($log_file) ) {
		$log_file = 'log_file';
	}
	if ( defined("APP_LOG_PATH" ) /*&& substr($log_file, 0, 1) != '/'*/) {
		$log_file = APP_LOG_PATH.$log_file;
	}

	if ( !file_exists($log_file) ) { 
		$fp = fopen($log_file, 'a');
	} else if ( filesize($log_file) > $log_size ) {
		$fp = fopen($log_file, 'w');
	} else {
		$fp = fopen($log_file, 'a');
	}

	if ( flock($fp, LOCK_EX) ) {
		$cip	= defined("CLIENT_IP") ? "[".CLIENT_IP."] " : '';
		$log_str = "[".date('Y-m-d H:i:s')."] ".$cip.$log_str."\r\n";
		fwrite($fp, $log_str);
		flock($fp, LOCK_UN);
	}
	fclose($fp);

	ignore_user_abort(FALSE);
}

/**
 * 防止用户恶意刷新请求 
 * 当用户在连续 $time 秒内请求超过 $count 次时,
 * 表示用户在恶意请求, 此时将在 $time 秒内拒绝用户的请求
 * 如果对不同项目需要不同了防刷新方式, 则应该要指定 $cc_name
 */
function prevent_cc($count = 5, $time = 10, $cc_name = 'hphp_cc', $callback = '') 
{
	if ( $_SESSION[$cc_name] == 1 || isset($_COOKIE[$cc_name]) ) {	/* 这里表示用户确实访问过 */
		/* 客户拒绝 cookie 将不能正常浏览, 这应该是可以接受的 */
		if ( $_COOKIE[$cc_name."_flag"] == 1 && $_COOKIE[$cc_name] < $count ) {	/* 接受请求 */
			setcookie($cc_name, $_COOKIE[$cc_name] + 1, time() + $time);
			return 0;
		} else {
			$_SESSION[$cc_name] == 0;
			if ( $callback ) {
				return $callback();
			}
			header("HTTP/1.1 503 Service Unavailable");
			exit;
		}
	} else {
		$_SESSION[$cc_name] = 1;
		setcookie($cc_name, 1, time() + $time, "/");
		setcookie($cc_name."_flag", 1, 0, "/");
	}

	return 0;
}

$lang['我是中文标题'] = '我是中文标';
$lang['中文~%s"%d<hr>'] = '中文~%s"%d<hr>';
$lang['请选择'] = 'select, please';
$lang['select,'] = '选择, please';
?>
<html>
<head>
<title>cache</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
$test  = "helo";
$test2 = "helo";
echo get_var_name($test2);
echo $test.'<br/>\n';
echo $test2.'<br/>\n';

var_dump($lang);
$test = array('aa'=>$lang, $_GET);
acache('tmp.php', $test);
acache('tmp1.inc.php', putcachevars($lang));
acache('tmp2.inc.php', $test, '.', '_cache_', 'define');
acache('tmp3.inc.php', $test, '.', '_cache_', 'var');
?>
</body>
</html>
