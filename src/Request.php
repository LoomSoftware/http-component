<?php

declare(strict_types=1);

namespace Loom\HttpComponent;

use Loom\HttpComponent\Traits\ResolveHeadersTrait;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{
    use ResolveHeadersTrait;

    private StreamInterface $body;

    public function __construct(
        private string $method,
        private UriInterface $uri,
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

    public function withProtocolVersion(string $version): MessageInterface
    {
        $request = clone $this;
        $request->protocolVersion = $version;

        return $request;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        return isset($this->headers[strtolower($name)]);
    }

    /**
     * @return string[]
     */
    public function getHeader(string $name): array
    {
        return $this->headers[strtolower($name)] ?? [];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader(string $name, $value): MessageInterface
    {
        $request = clone $this;
        $request->headers[strtolower($name)] = is_array($value) ? $value : [$value];

        return $request;
    }

    public function withAddedHeader(string $name, $value): MessageInterface
    {
        $name = strtolower($name);
        $request = clone $this;
        $request->headers[$name] = array_merge($this->headers[$name] ?? [], is_array($value) ? $value : [$value]);

        return $request;
    }

    public function withoutHeader(string $name): MessageInterface
    {
        $request = clone $this;
        unset($request->headers[strtolower($name)]);

        return $request;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        $request = clone $this;
        $request->body = $body;

        return $request;
    }

    public function getRequestTarget(): string
    {
        $path = $this->uri->getPath();
        $query = $this->uri->getQuery();

        return $path
            ? ($query ? sprintf('%s?%s', $path, $query) : $path)
            : ($query ? sprintf('/?%s', $query) : '/');
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        $request = clone $this;
        $request->uri = $request->uri->withPath(strtok($requestTarget, '?'));
        $request->uri = $request->uri->withQuery(substr(strstr($requestTarget, '?'), 1));

        return $request;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): RequestInterface
    {
        $request = clone $this;
        $request->method = $method;

        return $request;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        $request = clone $this;
        $request->uri = $uri;

        if (!$preserveHost) {
            $request->uri = $request->uri->withHost($request->getHeaderLine('host'));
        }

        return $request;
    }

    public function getFlatHeaders(): array
    {
        $headerStrings = [];

        foreach ($this->headers as $header => $values) {
            foreach ($values as $value) {
                $headerStrings[] = "$header: $value";
            }
        }

        return $headerStrings;
    }

    public function getData(): array
    {
        $contentType = $this->getHeaderLine('Content-Type');
        $bodyContents = $this->getBody()->getContents();

        if ($contentType === 'application/json') {
            $data = json_decode($bodyContents, true);
        } else if ($contentType === 'application/x-www-form-urlencoded') {
            parse_str($bodyContents, $data);
        } else {
            $data = [];
        }

        return $data;
    }

    public function get(string $key): mixed
    {
        return $this->getData()[$key] ?? null;
    }

    public function getQueryParam(string $name, ?string $default = null): ?string
    {
        return $this->getQueryParams()[$name] ?? $default;
    }

    protected function getQueryParams(): array
    {
        $queryParams = [];

        parse_str($this->uri->getQuery(), $queryParams);

        return $queryParams;
    }
}