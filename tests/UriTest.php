<?php

declare(strict_types=1);

namespace Loom\HttpComponentTests;

use Loom\HttpComponent\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

class UriTest extends TestCase
{
    public function testGetScheme(): void
    {
        $this->assertEquals('https', ($this->getDefaultUri())->getScheme());
    }

    public function testGetAuthority(): void
    {
        $uri = new Uri('https', 'test.com', '/path', 'var=1', 443);
        $this->assertEquals('test.com:443', $uri->getAuthority());

        $uri = new Uri('ftp', 'user:password@test.com', '/path', 'var=1');
        $this->assertEquals('user:password@test.com', $uri->getAuthority());
    }

    public function testWithPath(): void
    {
        $this->assertEquals('/', ($this->getDefaultUri())->getPath());
        $this->assertEquals('/new', ($this->getDefaultUri())->withPath('/new')->getPath());
    }

    public function testToString(): void
    {
        $this->assertEquals('https://test.com/', ($this->getDefaultUri())->__toString());
    }

    public function testWithUserInfo(): void
    {
        $this->assertEquals(
            'username:password',
            ($this->getDefaultUri())->withUserInfo('username', 'password')->getUserInfo()
        );
    }

    public function testWithScheme(): void
    {
        $this->assertEquals('ftp', ($this->getDefaultUri())->withScheme('ftp')->getScheme());
    }

    public function testWithHost(): void
    {
        $this->assertEquals('localhost', ($this->getDefaultUri())->withHost('localhost')->getHost());
    }

    public function testWithPort(): void
    {
        $this->assertEquals(8080, ($this->getDefaultUri())->withPort(8080)->getPort());
    }

    public function testWithQuery(): void
    {
        $this->assertEquals(
            'name=test&hello=world',
            ($this->getDefaultUri())->withQuery('name=test&hello=world')->getQuery()
        );
    }

    public function testWithFragment(): void
    {
        $this->assertEquals('news', ($this->getDefaultUri())->withFragment('news')->getFragment());
    }

    private function getDefaultUri(): UriInterface
    {
        return new Uri('https', 'test.com', '/', '');
    }
}