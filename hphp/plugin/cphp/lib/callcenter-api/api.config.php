<?php
//define('API_DEBUG', 'run');
define('API_DEBUG', 'debug');
if ( !defined('CFG_AGI_HOST_PBX') ) {
	define('CFG_AGI_HOST_PBX', 'localhost');
	//define('CFG_AGI_HOST_PBX', '192.168.1.232');
	define('CFG_AGI_USER_PBX', 'admin');
	define('CFG_AGI_PWD_PBX', 'amp111');
}

/* ����У���IP */
$api_config['allow'] = '*';
//$api_config['allow'] = '192.168.1.226,127.0.0.1';
//$api_config['allow'] = '0';
/* ����ID, ��δʹ�� */
$api_config['bid'] = '1002';
/* У�� key: chksum = md5(action+srcno+dstno+md5(client_ip+'@'+key)) */
$api_config['key'] = 'DeL>rty<:JKO#:k+_p';
/* nusoap Web Server �׼�·��(����ڴ������ļ���·�������·��): */
$api_config['nusoap_path'] = '../nusoap/nusoap.php';

/* end file */
?>
