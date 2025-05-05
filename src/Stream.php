<?php

declare(strict_types=1);

namespace Loom\HttpComponent;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    public function __construct(private $resource)
    {
        if (!is_resource($this->resource)) {
            throw new \InvalidArgumentException('Invalid resource provided for Stream');
        }

        $this->rewind();
    }

    public function __toString(): string
    {
        return $this->getContents();
    }

    public function close(): void
    {
        if (is_resource($this->resource)) {
            fclose($this->resource);
        }
    }

    /**
     * @return resource
     */
    public function detach(): mixed
    {
        $resource = $this->resource;
        $this->resource = null;

        return $resource;
    }

    public function getSize(): ?int
    {
        if (!$this->resource) {
            return null;
        }

        $stats = fstat($this->resource);

        return $stats['size'] ?? null;
    }

    public function tell(): int
    {
        if (!$this->resource) {
            throw new \RuntimeException('Stream is detached');
        }

        $position = ftell($this->resource);

        if ($position === false) {
            throw new \RuntimeException('Unable to get the stream position');
        }

        return $position;
    }

    public function eof(): bool
    {
        if (!$this->resource) {
            throw new \RuntimeException('Stream is detached');
        }

        return feof($this->resource);
    }

    public function isSeekable(): bool
    {
        if (!$this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);

        return isset($meta['seekable']) && $meta['seekable'];
    }

    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!$this->isSeekable()) {
            throw new \RuntimeException('Stream is not seekable');
        }

        if (fseek($this->resource, $offset, $whence) === -1) {
            throw new \RuntimeException('Unable to seek to stream position ' . $offset);
        }
    }

    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        if (!$this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);

        return isset($meta['mode']) && (str_contains($meta['mode'], 'w') || str_contains($meta['mode'], 'a'));
    }

    public function write(string $string): int
    {
        if (!$this->isWritable()) {
            throw new \RuntimeException('Stream is not writable');
        }

        $result = fwrite($this->resource, $string);

        if ($result === false) {
            throw new \RuntimeException('Error writing to stream');
        }

        return $result;
    }

    public function isReadable(): bool
    {
        if (!$this->resource) {
            return false;
        }

        $meta = stream_get_meta_data($this->resource);

        return isset($meta['mode']) && (str_contains($meta['mode'], 'r') || str_contains($meta['mode'], 'a') || str_contains($meta['mode'], '+'));
    }

    public function read(int $length): string
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException('Stream is not readable');
        }

        $data = fread($this->resource, $length);

        if ($data === false) {
            throw new \RuntimeException('Error reading from stream');
        }

        return $data;
    }

    public function getContents(): string
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException('Stream is not readable');
        }

        $contents = stream_get_contents($this->resource);

        if ($contents === false) {
            throw new \RuntimeException('Error getting stream contents');
        }

        $this->rewind();

        return $contents;
    }

    public function getMetadata(mixed $key = null): mixed
    {
        if (!$this->resource) {
            return $key ? null : [];
        }

        $meta = stream_get_meta_data($this->resource);

        if ($key === null) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }
}