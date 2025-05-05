<?php

namespace Loom\HttpComponent;

use Loom\HttpComponent\Traits\ResolveHeadersTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{
    use ResolveHeadersTrait;

    private int $statusCode;
    private string $reasonPhrase;
    private array $headers;
    private StreamInterface $body;
    private string $protocolVersion;

    public function __construct(
        int $statusCode = 200,
        string $reasonPhrase = '',
        array $headers = [],
        StreamInterface $body = null,
        string $protocolVersion = '1.1'
    ) {
        $this->statusCode = $statusCode;
        $this->reasonPhrase = $reasonPhrase;
        $this->headers = $this->setHeaders($headers);
        $this->body = $body ?? new Stream(fopen('php://temp', 'r+'));
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @param string $version
     * 
     * @return ResponseInterface
     */
    public function withProtocolVersion(string $version): ResponseInterface
    {
        $response = clone $this;
        $response->protocolVersion = $version;

        return $response;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string $name
     * 
     * @return bool
     */
    public function hasHeader(string $name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    /**
     * @param string $name
     * 
     * @return array|string[]
     */
    public function getHeader(string $name): array
    {
        return $this->headers[strtolower($name)] ?? [];
    }

    /**
     * @param string $name
     * 
     * @return string
     */
    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    /**
     * @param string $name
     * @param $value
     * 
     * @return ResponseInterface
     */
    public function withHeader(string $name, $value): ResponseInterface
    {
        $response = clone $this;
        $name = strtolower($name);
        $response->headers[$name] = is_array($value) ? $value : [$value];

        return $response;
    }

    /**
     * @param string $name
     * @param $value
     * 
     * @return ResponseInterface
     */
    public function withAddedHeader(string $name, $value): ResponseInterface
    {
        $response = clone $this;
        $name = strtolower($name);
        $response->headers[$name] = array_merge($this->headers[$name] ?? [], is_array($value) ? $value : [$value]);

        return $response;
    }

    /**
     * @param string $name
     *
     * @return ResponseInterface
     */
    public function withoutHeader(string $name): ResponseInterface
    {
        $response = clone $this;
        $name = strtolower($name);
        unset($response->headers[$name]);

        return $response;
    }

    /**
     * @return StreamInterface
     */
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    /**
     * @param StreamInterface $body
     *
     * @return ResponseInterface
     */
    public function withBody(StreamInterface $body): ResponseInterface
    {
        $response = clone $this;
        $response->body = $body;

        return $response;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param $code
     * @param $reasonPhrase
     *
     * @return ResponseInterface
     */
    public function withStatus($code, $reasonPhrase = ''): ResponseInterface
    {
        $response = clone $this;
        $response->statusCode = $code;
        $response->reasonPhrase = $reasonPhrase;

        return $response;
    }

    /**
     * @return string
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }
}