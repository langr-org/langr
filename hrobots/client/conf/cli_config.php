<?php
/* D:\wamp\bin\php\php5.3.13\php -f */

//if ( !defined('APP_DEBUG') ) { define('APP_DEBUG', true); }
if ( !defined('APP_PATH') ) { define('APP_PATH', dirname(dirname(__FILE__)).'/'); }
if ( !defined('H2H_COOKIE_PATH') ) { define('H2H_COOKIE_PATH', './logs/'); }
if ( !defined('APP_LOG_PATH') ) { define('APP_LOG_PATH', './logs/'); }

/**
 * 取服务器接口地址信息，备用，
 * 当$api_config['server'] 不可用(临时改变)时，客户端从此处取新的服务器地址信息
 * @see server/api/beat
 */
//$api_config['server'] = 'http://robots.kits.com/api';
//$api_config['server'] = 'http://192.168.100.58/server/api';
$api_config['server'] = 'http://192.168.200.202:10000/server/api';
$api_config['serverinfo'] = 'http://www.kitsmall.com/dynamic_ip/ip.php?act=serverinfo&name=hrobots';

/* 版本 */
$api_config['version'] = '2.1';
/** 校检 key 文件:
 * client.key 文件由服务端生成，内容: username@client_key
 * @see server/Conf/api_config.php $api_config['key'].
 */
$api_config['key'] = 'client.key';

/* 客户端已经安装了的采集模块 */
//$api_config['collect_module'] = array('digikey', 'mouser', 'future', 'newark', 'element14', 'avnet', 'arrow', 'rs', 'tti');

/* 采集优先策略 */
$api_config['priority'] = array(
	'do'=>'',		/* Non-support! 采集优先动作: collect|update, 没指定则随机 */
	'module'=>'',		/* Non-support! 优先模块: 没指定则随机 */
	'retry'=>5,	 	/* socket 连接出错时，最多尝试retry次，则跳过此数据 */
);

/* end file */
