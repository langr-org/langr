<?php

//if ( !defined('APP_DEBUG') ) { define('APP_DEBUG', true); }
if ( !defined('API_DEBUG') ) { define('API_DEBUG', false); }
if ( !defined('H2H_COOKIE_PATH') ) { define('H2H_COOKIE_PATH', LOG_PATH); }
if ( !defined('APP_LOG_PATH') ) { define('APP_LOG_PATH', LOG_PATH); }

/* server 在服务器web目录中的路径，web服务器为非80端口时还需要指定端口，为空时，会默认'/api' */
//$api_config['server_path'] = ':10000/api';

/* 服务器端版本 */
$api_config['version'] = '0.1';
/* 不用校检的IP */
//$api_config['allow'] = '*';		/* 全部允许 */
//$api_config['allow'] = '192.168.1.226,127.0.0.1';
$api_config['allow'] = '0';		/* 全部校检 */
/** 校检 key: chksum = md5(client_random+client_key) 
 * 	client_key = md5(username+'@'+key) client_key 在服务器端生成并保存在客户端，客户端不知校检key
 * 	客户端需要向服务器索取或申请 username & client_key
 * 	username 默认可为域名
 */
$api_config['key'] = 'DeL>rt%y<J#:k+_p';

$api_config['max_size'] = '4194304';	/* 4M */
//$api_config['max_size'] = '2097152';	/* 2M */
$api_config['allow_type'] = array('image/png'=>'.png','image/jpeg'=>'.jpg','image/gif'=>'.gif','application/octet-stream'=>'.gif');
//$api_config['allow_type'] = 'image/jpeg,image/png,image/gif';

$api_config['dir_rule'] = date('Ym/dH/');		/* 图片保存的目录规则 */

if ( !defined('CLIENT_IP') ) { define('CLIENT_IP', getenv('HTTP_X_FORWARDED_FOR') ? getenv('HTTP_X_FORWARDED_FOR') : getenv('REMOTE_ADDR')); }

/* end file */
