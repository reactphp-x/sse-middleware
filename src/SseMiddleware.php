<?php

namespace Reactphp\Framework\SseMiddleware;

use Psr\Http\Message\ServerRequestInterface;
use React\Stream\ThroughStream;
use React\Http\Message\Response;

class SseMiddleware
{
    protected $stream;
    protected $headers;

    public function __construct(ThroughStream $stream, $headers = [])
    {
        $this->stream = $stream;
        $this->headers = $headers;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        return new Response(
            Response::STATUS_OK,
            [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
            ] + $this->headers,
            $this->stream
        );
    }
}
