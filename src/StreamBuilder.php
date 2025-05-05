<?php

declare(strict_types=1);

namespace Loom\HttpComponent;

use Psr\Http\Message\StreamInterface;

class StreamBuilder
{
    public static function build(string $body): StreamInterface
    {
        $stream = new Stream(fopen('php://temp', 'r+'));
        $stream->write($body);
        $stream->rewind();

        return $stream;
    }
}