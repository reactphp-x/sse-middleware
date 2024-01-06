<?php

require __DIR__ . '/../vendor/autoload.php';

use Reactphp\Framework\SseMiddleware\SseMiddleware;
use Reactphp\Framework\SseMiddleware\ServerSentEvents;
use React\Stream\ThroughStream;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;

$http = new React\Http\HttpServer(function (ServerRequestInterface $request) {

    $stream = new ThroughStream();

    $timer = Loop::addPeriodicTimer(1, function () use ($stream) {
        $stream->write(new ServerSentEvents(['data' => date('Y-m-d H:i:s')]));
    });

    $stream->on('close', function () use ($timer) {
        Loop::cancelTimer($timer);
    });

    Loop::addTimer(10, function () use ($stream) {
        $stream->end();
    });

    return (new SseMiddleware($stream, [
        'Access-Control-Allow-Origin' => '*',
    ]))($request);
});
$socket = new React\Socket\SocketServer('0.0.0.0:8090');
$http->listen($socket);
