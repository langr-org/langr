<?php
/**
 * run with command 
 * php start_web.php start
 * 启动 web
 */

use Workerman\Worker;
use Workerman\Protocols\Http\Response;
require_once __DIR__ . '/Workerman/Autoloader.php';

/**
 * 首先会查看用户是否有自定义\Protocols\Http协议类，
 * 如果没有使用workerman内置协议类Workerman\Protocols\Http
 */
$web = new Worker('http://0.0.0.0:8080');
$web->name = 'web';

const WEB_PATH = './web/';

/**
 * 新连接，登陆认证，保存连接信息。
 */
$web->onConnect = function($connection)
{
    $connection->id = 'C-'.date('His');
    $connection->uid = 'null';
};

$web->onMessage = function($connection, $request)
{
    //var_dump("\nmessage\n", $request->get(), $request->header(), $request->path());
    $connection->uid = 'A-'.date('His');

    $body = '404';
    $web_file = WEB_PATH.$request->path();
    if (is_file($web_file)) {
        $body = file_get_contents($web_file);
    }
    //$connection->send("world ".date('Y-m-d H:i:s'));
    $response = new Response(200, [
        'Content-Type' => 'text/html',
        'Server' => 'gcweb/1.0'
    ], $body);
    $connection->send($response);
};

$web->onClose = function($connection)
{
    echo $connection->id." cid \n";
    echo $connection->uid." connection closed\n";
};

// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}

/* end file */
