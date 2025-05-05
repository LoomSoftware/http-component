<?php

declare(strict_types=1);

namespace Loom\HttpComponent;

use Loom\HttpComponent\Traits\ResolveHeadersTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{
    use ResolveHeadersTrait;

    private StreamInterface $body;

    public function __construct(
        private int $statusCode = 200,
        private string $reasonPhrase = 'OK',
        private array $headers = [],
        ?StreamInterface $body = null,
        private string $protocolVersion = '1.1'
    ) {
        $this->headers = $this->setHeaders($headers);
        $this->body = $body ?? new Stream(fopen('php://temp', 'r+'));
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): ResponseInterface
    {
        $response = clone $this;
        $response->protocolVersion = $version;

        return $response;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    public function getHeader(string $name): array
    {
        return $this->headers[strtolower($name)] ?? [];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader(string $name, $value): ResponseInterface
    {
        $response = clone $this;
        $name = strtolower($name);
        $response->headers[$name] = is_array($value) ? $value : [$value];

        return $response;
    }

    public function withAddedHeader(string $name, $value): ResponseInterface
    {
        $response = clone $this;
        $name = strtolower($name);
        $response->headers[$name] = array_merge($this->headers[$name] ?? [], is_array($value) ? $value : [$value]);

        return $response;
    }

    public function withoutHeader(string $name): ResponseInterface
    {
        $response = clone $this;
        $name = strtolower($name);
        unset($response->headers[$name]);

        return $response;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): ResponseInterface
    {
        $response = clone $this;
        $response->body = $body;

        return $response;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function withStatus($code, $reasonPhrase = ''): ResponseInterface
    {
        $response = clone $this;
        $response->statusCode = $code;
        $response->reasonPhrase = $reasonPhrase;

        return $response;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }
}