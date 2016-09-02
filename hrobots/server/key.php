<?php
header('content-type:text/html;charset=utf-8');
$download_url = 'api/download?type=full';
@include('Conf/api_config.php');
if ( empty($_POST['user']) && empty($_GET['u']) ) {
	echo <<<EOT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<title>HQrobots</title>
</head>
<body style="">
<div style="size:38px;text-align:center;margin:20px;"><h1>HQPG HQrobots {$api_config['client_version']} 客户端</h1></div>
<div style="position:relative;left:10%;top:5%;width:80%;padding:5px;border:1px solid red">
<div style="left:30px;padding:15px;border:1px solid blue">
<div >客户端下载：</div>
<a href="$download_url">点击此处下载最新版本客户端</a>
</div>
<div style="margin-top:5px;padding:15px;border:1px solid blue">
<div >客户端密匙生成：</div>
<form action="key.php" method="post">
<table>
<tr>
<td>请输入用户名</td>
<td><input type="text" name="user" maxlength="12"/></td>
<td><input type="submit" value="提交" /></td>
</tr>
</table>
</form>
<div style="position:absolute;right:5%;bottom:15px">内部程序，如需帮助请联络：<a href="mailto:hua@langr.org">hua@langr.org</a></div>
</div>
</div>
</body>
</html>
EOT;
	exit;
}

$user = empty($_GET['u']) ? 'HQ'.date('ymd').'_'.$_POST['user'] : $_GET['u'];
$key = md5($user.'@'.$api_config['key']);
$content = $user.'@'.$key;
$file = 'Runtime/tmp/client.key.'.$user;
if (!file_exists($file)) {
	file_put_contents($file, $content);
}
if ( !empty($user) ) {
	header("Content-type:application/txt");
	header("Content-Disposition:attachment;filename=client.key");
	readfile($file);
	exit;
}

