# reactphhp-frameework-sse-middleware

## install
```
composer require reactphp-x/sse-middleware -vvv
```

## Usage
```php
<?php

require __DIR__ . '/../vendor/autoload.php';

use ReactphpX\SseMiddleware\SseMiddleware;
use ReactphpX\SseMiddleware\ServerSentEvents;
use React\Stream\ThroughStream;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;

$http = new React\Http\HttpServer(function (ServerRequestInterface $request) {

    $stream = new ThroughStream();

    $timer = Loop::addPeriodicTimer(1, function () use ($stream) {
        //data for example ['event'=>'ping', 'data' => 'some thing', 'id' => 1000, 'retry' => 5000]
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
```

## License
MIT