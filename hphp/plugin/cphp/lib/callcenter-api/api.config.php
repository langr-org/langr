<?php
//define('API_DEBUG', 'run');
define('API_DEBUG', 'debug');
if ( !defined('CFG_AGI_HOST_PBX') ) {
	define('CFG_AGI_HOST_PBX', 'localhost');
	//define('CFG_AGI_HOST_PBX', '192.168.1.232');
	define('CFG_AGI_USER_PBX', 'admin');
	define('CFG_AGI_PWD_PBX', 'amp111');
}

/* 不用校检的IP */
$api_config['allow'] = '*';
//$api_config['allow'] = '192.168.1.226,127.0.0.1';
//$api_config['allow'] = '0';
/* 接入ID, 暂未使用 */
$api_config['bid'] = '1002';
/* 校检 key: chksum = md5(action+srcno+dstno+md5(client_ip+'@'+key)) */
$api_config['key'] = 'DeL>rty<:JKO#:k+_p';
/* nusoap Web Server 套件路径(相对于此配置文件的路径或绝对路径): */
$api_config['nusoap_path'] = '../nusoap/nusoap.php';

/* end file */
?>
