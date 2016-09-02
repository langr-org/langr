<?php
/* #!/usr/local/bin/php -f */

//if ( !defined('APP_DEBUG') ) { define('APP_DEBUG', true); }
//if ( !defined('API_DEBUG') ) { define('API_DEBUG', true); }
if ( !defined('H2H_COOKIE_PATH') ) { define('H2H_COOKIE_PATH', LOG_PATH); }
if ( !defined('APP_LOG_PATH') ) { define('APP_LOG_PATH', LOG_PATH); }

/* 心跳地址，服务器端为动态ip时需要定时访问此接口 */
$api_config['server_beat'] = 'http://www.kitsmall.com/dynamic_ip/ip.php';
/* server 在服务器web目录中的路径，web服务器为非80端口时还需要指定端口，为空时，会默认'/server/api' */
$api_config['server_path'] = ':10000/server/api';

/* 服务器端版本 */
$api_config['version'] = '1.92';
/* 允许的客户端版本 */
$api_config['allow_client'] = 'v1.5,v1.6,v1.7,v1.8,v1.81,v2.0,v2.1';
/* 最新的客户端程序包及版本号: full: xxx.full.zip, update: xxx.update.zip */
$api_config['client_version'] = '2.0';
$api_config['client_zip'] = 'HQrobots.v2.0.full.zip';
/* 不用校检的IP */
//$api_config['allow'] = '*';		/* 全部允许 */
//$api_config['allow'] = '192.168.1.226,127.0.0.1';
$api_config['allow'] = '0';		/* 全部校检 */

/** 校检 key: chksum = md5(client_id+'@'+client_key) 
 * 	client_id 由客户端取得并传到服务端:
 * 		$client_id = getenv('COMPUTERNAME') ? getenv('COMPUTERNAME') : '@'.getenv('USER');
 * 		$client_id = $client_id.'@'.dirname(__FILE__);
 * 	client_key = md5(username+'@'+key) client_key 在服务器端生成并保存在客户端，客户端不知校检key
 * 	username 为客户端在下载时，自动生成或手动指定的，可以重复的客户用户名
 * 	username, client_key 存放在 client.key 中: username@client_key
 * 	client_id 可看作采集机器身份，username 采集客户身份，client_key 采集客户key, key 服务器校检key
 */
$api_config['key'] = 'DHeL>rt%y<:JKO#:k+_p';

/* 可以调度的动作和模块 */
$api_config['collect_do'] = array('collect', 'update');
$api_config['collect_module'] = array('mouser','digikey','newark');
//$api_config['collect_module'] = array('digikey', 'mouser', 'future', 'newark', 'element14', 'avnet', 'arrow', 'rs', 'tti');

/* 采集优先策略, 即时更改即时生效 */
$api_config['priority'] = array(
	'do'=>'collect',	/* 采集优先动作: collect|update, 没指定则随机 */
	'module'=>'newark',		/* 优先模块: 没指定则随机 */
	'hot'=>'', 		/* 热门顺序 */
	'category'=>'', 	/* 优先分类 */
	'task'=>30,	 	/* 每个 task 包大小 */
	'retry'=>5,	 	/* socket 连接出错时，最多尝试retry次，则跳过此数据 */
	'time'=>48*60*60,	/* 新数据缓存时间，time 时间内更新过的数据不再更新 */
);

//$api_config['big_host'] = "http://192.168.100.201:9998";
$api_config['big_host'] = "http://183.62.107.205:9998";

if ( !defined('CLIENT_IP') ) { define('CLIENT_IP', getenv('HTTP_X_FORWARDED_FOR') ? getenv('HTTP_X_FORWARDED_FOR') : getenv('REMOTE_ADDR')); }
/* end file */
