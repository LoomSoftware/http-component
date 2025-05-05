<?php

declare(strict_types=1);

namespace Loom\HttpComponent;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface
{
    private ?string $port;
    private string $fragment;
    private string $userInfo;

    public function __construct(
        private string $scheme,
        private string $host,
        private string $path,
        private string $query,
        string|int|null $port = null
    ) {
        $this->port = (string) $port;
        $this->fragment = '';
        $this->userInfo = '';
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function getAuthority(): string
    {
        $authority = $this->host;

        if (!empty($this->userInfo)) {
            $authority = sprintf('%s@%s', $this->userInfo, $authority);
        }

        if (!empty($this->port)) {
            $authority = sprintf('%s:%s', $authority, $this->port);
        }

        return $authority;
    }

    public function getUserInfo(): string
    {
        return $this->userInfo;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return (int) $this->port;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getFragment(): string
    {
        return $this->fragment;
    }

    public function withScheme(string $scheme): UriInterface
    {
        $uri = clone $this;
        $uri->scheme = $scheme;

        return $uri;
    }

    public function withUserInfo(string $user, ?string $password = null): UriInterface
    {
        $uri = clone $this;
        $uri->userInfo = $password ? sprintf('%s:%s', $user, $password) : $user;

        return $uri;
    }

    public function withHost(string $host): UriInterface
    {
        $uri = clone $this;
        $uri->host = $host;

        return $uri;
    }

    public function withPort(?int $port): UriInterface
    {
        $uri = clone $this;
        $uri->port = (string) $port;

        return $uri;
    }

    public function withPath(string $path): UriInterface
    {
        $uri = clone $this;
        $uri->path = $path;

        return $uri;
    }

    public function withQuery(string $query): UriInterface
    {
        $uri = clone $this;
        $uri->query = $query;

        return $uri;
    }

    public function withFragment(string $fragment): UriInterface
    {
        $uri = clone $this;
        $uri->fragment = $fragment;

        return $uri;
    }

    public function __toString(): string
    {
        $uri = sprintf('%s://%s', $this->scheme, $this->host);

        if (!empty($this->port)) {
            $uri .= sprintf(':%d', $this->port);
        }

        $uri .= $this->path;

        if (!empty($this->query)) {
            $uri .= sprintf('?%s', $this->query);
        }

        if (!empty($this->fragment)) {
            $uri .= sprintf('#%s', $this->fragment);
        }

        return $uri;
    }
}