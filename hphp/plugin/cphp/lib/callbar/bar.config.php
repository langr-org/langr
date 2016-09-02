<?php
define('API_DEBUG', 'debug');

$config['p_url'] = '/ecshop/admin/order.php?act=pop';//'srcno=&dstno=&type=';
$config['p_target'] = 'main-frame';
$config['p_type'] = 'npage';	/*'div';*/
$config['index_page'] = '../index.php?act=main';

/* callcenter api 接口需要, 一般不用配置 */
$config['api_key'] = 'DeL>rty<:JKO#:k+_p';
$config['api_url'] = 'http://192.168.1.226/callcenter/app/control/index/api.class.php';

/* end file */
if ( API_DEBUG == 'debug' && !$_COOKIE['callbar_extension'] ) {
	setcookie('callbar_extension', '808', 0, '/');
	setcookie('callbar_name', "800008", 0, '/');
}

