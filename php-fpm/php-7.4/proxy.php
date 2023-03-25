<?php
use Swoole\Constant;
use Swoole\Coroutine\FastCGI\Proxy;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

// $documentRoot = '/var/www/html'; # WordPress项目根目录
$documentRoot = $_ENV['SWOOLE_PROXY_DOCUMENT_ROOT']; # WordPress项目根目录
$server = new Server('0.0.0.0', $_ENV['SWOOLE_PROXY_PORT'], SWOOLE_BASE); # 这里端口需要和WordPress配置一致, 一般不会特定指定端口, 就是80
$server->set([
    Constant::OPTION_WORKER_NUM => swoole_cpu_num() * 2,
    Constant::OPTION_HTTP_PARSE_COOKIE => false,
    Constant::OPTION_HTTP_PARSE_POST => false,
    Constant::OPTION_DOCUMENT_ROOT => $documentRoot,
    Constant::OPTION_ENABLE_STATIC_HANDLER => true
]);
$proxy = new Proxy('127.0.0.1:9000', $documentRoot); # 建立代理对象
$server->on('request', function (Request $request, Response $response) use ($proxy) {
    $proxy->pass($request, $response); # 一键代理请求
});
$server->start();