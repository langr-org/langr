<?php
/**
 * run with command 
 * php start_websocket.php start
 * 启动 websocket 服务器端
 * 
 * @author xxx <xxx@xxx.org> 2020/05/16 21:37
 * 
 * $Id$
 */

use Workerman\Worker;
use Workerman\Lib\Timer;

const HEARTBEAT_TIME = 60;
const DO_LOGIN_TIME = 10;
const LOG_PATH = 'log/';

require_once __DIR__ . '/Workerman/Autoloader.php';
require_once __DIR__ . '/app/lib.php';
require_once __DIR__ . '/app/Room.class.php';

/**
 * 首先会查看用户是否有自定义\Protocols\Http协议类，
 * 如果没有使用workerman内置协议类Workerman\Protocols\Http
 */
$worker = new Worker('websocket://0.0.0.0:8765');
// 启动1个进程，同时监听8765端口，以websocket协议提供服务
$worker->count = 1;
$worker->name = 'websocket';

$room = new Room();

/**
 * 新连接，登陆认证，保存连接信息。
 */
$worker->onConnect = function($connection)
{
    $connection->id = date('CHis');
    $connection->uid = '';
    $connection->lastMessageTime = time();
    $connection->send("hello ".date('Y-m-d H:i:s'));
    /* 超时不认证就关闭 */
    $timer_id = Timer::add(DO_LOGIN_TIME, function() use (& $timer_id, & $connection) {
        if (empty($connection->uid)) {
            Timer::del($timer_id);
            $connection->send('close by server.');
            $connection->close();
        }
    });
};

$worker->onMessage = function($connection, $data)
{
    global $room;
    //var_dump($_GET, $data);
    //$connection->send(date('YmdHis ').$data);
    $room->run($connection, $data);
};

$worker->onClose = function($connection)
{
    echo $connection->id." cid \n";
    echo $connection->uid." connection closed\n";
};

// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}

/* end file */
