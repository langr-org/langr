<?php
/**
 * run with command 
 * php start_web.php start
 * 启动 web
 */

use Workerman\Worker;
use Workerman\Protocols\Http\Response;
require_once __DIR__ . '/Workerman/Autoloader.php';

const WEB_PATH = './web/';
$ext_type = [
    'js' => 'application/javascript', 
    'htm' => 'text/html', 
    'html' => 'text/html',
    'txt' => 'text/html',
    'css' => 'text/css',
    'mp3' => 'audio/mp3',
    'mp4' => 'video/mpeg4',
    'gif' => 'image/gif',
    'jpg' => 'image/jpeg',
    'png' => 'image/png',
    'ico' => 'image/x-icon',
    'svg' => 'image/svg+xml',
    'xml' => 'text/xml',
    'apk' => 'application/vnd.android.package-archive',
    'ipa' => 'application/vnd.iphone',
    '*' => 'application/octet-stream',
];

/**
 * 首先会查看用户是否有自定义\Protocols\Http协议类，
 * 如果没有使用workerman内置协议类Workerman\Protocols\Http
 */
$web = new Worker('http://0.0.0.0:8080');
$web->name = 'web';

/**
 * 新连接，登陆认证，保存连接信息。
 */
$web->onConnect = function($connection)
{
    $connection->uid = 'C-'.date('His');
};

$web->onMessage = function($connection, $request)
{
    global $ext_type;
    //var_dump("\nmessage\n", $request->get(), $request->header(), $request->path());

    $body = '';
    $file_ext = 'html';
    $content_type = 'text/html';
    $web_file = WEB_PATH.$request->path();
    $file_ext = pathinfo($web_file, PATHINFO_EXTENSION);
    if (!empty($ext_type[$file_ext])) {
        $content_type = $ext_type[$file_ext];
    }
    if (is_file($web_file)) {
        $body = file_get_contents($web_file);
    }
    //$connection->send("world ".date('Y-m-d H:i:s'));
    $response = new Response(200, [
        'Content-Type' => $content_type,
        'Server' => 'YouWeb/1.0'
    ], $body);
    $connection->send($response);
};

$web->onClose = function($connection)
{
    echo $connection->uid." connection closed\n";
};

// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}

/* end file */
