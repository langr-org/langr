<?php
session_start();

$lang['我是中文标题'] = '我是中文标';
$lang['中文~%s"%d<hr>'] = '中文~%s"%d<hr>';
$lang['请选择'] = 'select, please';
$lang['select,'] = '选择, please';

function get_lang($lang = '') {
	if ( empty($lang) ) {
		if ( isset($_GET['l']) && !empty($_GET['l']) ) {
			$lang = strtolower($_GET['l']);
		} else {
			$lang = strtolower(substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 5));
		}
	} else {
		$lang = strtolower($lang);
	}

	$lang_file = APP_PATH.'/lang/'.$lang.'.lang.php';
	//$core_lang = CORE_PATH.'/lang/'.$lang_file;
	echo $lang_file;
	if ( file_exists($lang_file) ) {
		require($lang_file);
		return true;
	} else {
		/* default lang */
		//require(APP_PATH.'/lang/'defalut.lang.php);
		return false;
	}
}

function tr($str)
{
	global $lang;
	echo "tr($str)={$lang[$str]};&nbsp;";
	if ( empty($lang[$str]) ) {
		return $str;
	}
	return $lang[$str];
}
?>
<html>
<head>
<title>lang</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
echo get_lang().'&nbsp; end<br>';
echo get_lang('en-us').'&nbsp; end<br>';
echo tr('我是中文标题').'&nbsp; end<br>';
echo tr('我是中文').'&nbsp; end<br>';
echo tr('请选择').'&nbsp; end<br>';
echo tr('select,').'&nbsp; end<br>';
echo sprintf(tr('中文~%s"%d<hr>'), 'chinese', 888).'&nbsp; end<br>';
var_dump($lang);
?>
</body>
</html>
