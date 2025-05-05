<?php

namespace Loom\HttpComponentTests;

use Loom\HttpComponent\Request;
use Loom\HttpComponent\Stream;
use Loom\HttpComponent\StreamBuilder;
use Loom\HttpComponent\Uri;
use Loom\HttpComponentTests\Traits\ProvidesHeaderDataTrait;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    use ProvidesHeaderDataTrait;

    /**
     * @return void
     *
     * @throws MockObjectException
     */
    public function testGetHeaders(): void
    {
        $request = $this->simpleGetRequest();

        $this->assertEquals(['content-type' => ['application/json']], $request->getHeaders());
    }

    /**
     * @return void
     *
     * @throws MockObjectException
     */
    public function testMultipleHeaders(): void
    {
        [$uri, $body] = $this->getMockObjects();

        $request = new Request('GET', $uri, ['Content-Type' => ['application/json', 'text/plain']], $body);

        $this->assertEquals(['content-type' => ['application/json', 'text/plain']], $request->getHeaders());
    }

    /**
     * @return void
     *
     * @throws MockObjectException
     */
    public function testWithHeader(): void
    {
        $request = $this->simpleGetRequest();

        $request = $request->withHeader('Content-Type', 'text/html');

        $this->assertEquals(['content-type' => ['text/html']], $request->getHeaders());
    }

    /**
     * @return void
     *
     * @throws MockObjectException
     */
    public function testWithAddedHeader(): void
    {
        $request = $this->simpleGetRequest();

        $request = $request->withAddedHeader('Content-Type', 'text/html');

        $this->assertEquals(['content-type' => ['application/json', 'text/html']], $request->getHeaders());
    }

    /**
     * @param string $key
     *
     * @dataProvider headerKeyProvider
     *
     * @return void
     *
     * @throws MockObjectException
     */
    public function testHasHeader(string $key): void
    {
        $request = $this->simpleGetRequest();

        $this->assertTrue($request->hasHeader($key));
    }

    /**
     * @return void
     *
     * @throws MockObjectException
     */
    public function testWithoutHeader(): void
    {
        $request = $this->simpleGetRequest();

        $request = $request->withoutHeader('CONTENT-TYPE');

        $this->assertEquals([], $request->getHeaders());
    }

    /**
     * @return void
     *
     * @throws MockObjectException
     */
    public function testGetHeaderLine(): void
    {
        $request = $this->simpleGetRequest();

        $this->assertEquals('application/json', $request->getHeaderLine('content-type'));
    }

    /**
     * @param string $method
     *
     * @dataProvider methodProvider
     *
     * @return void
     *
     * @throws MockObjectException
     */
    public function testWithMethod(string $method): void
    {
        $request = $this->simpleGetRequest();

        $request = $request->withMethod($method);

        $this->assertEquals($method, $request->getMethod());
    }

    /**
     * @return void
     */
    public function testGetRequestTarget(): void
    {
        $uri = new Uri('https', 'localhost', '/test', 'hello=world');
        $body = StreamBuilder::build('test');
        $request = new Request('GET', $uri, ['Content-Type' => 'application/json'], $body);

        $this->assertEquals('/test?hello=world', $request->getRequestTarget());
    }

    /**
     * @return void
     */
    public function testWithRequestTarget(): void
    {
        $uri = new Uri('https', 'localhost', '/test', 'hello=world');
        $body = StreamBuilder::build('test');
        $request = new Request('GET', $uri, ['Content-Type' => 'application/json'], $body);

        $request = $request->withRequestTarget('/default?var=1');

        $this->assertEquals('/default?var=1', $request->getRequestTarget());
    }

    /**
     * @return void
     *
     * @throws MockObjectException
     */
    public function testWithUri(): void
    {
        $request = $this->simpleGetRequest();
        $uri = new Uri('https', 'localhost', '/test', 'hello=world');

        $request = $request->withUri($uri, true);

        $this->assertSame($uri, $request->getUri());
    }

    /**
     * @return void
     *
     * @throws MockObjectException
     */
    public function testGetData(): void
    {
        // application/json
        $uri = $this->createMock(Uri::class);
        $body = StreamBuilder::build('{"key":"value"}');
        $request = new Request('GET', $uri, ['Content-Type' => 'application/json'], $body);
        $this->assertEquals(['key' => 'value'], $request->getData());

        // application/x-www-form-urlencoded
        $body = StreamBuilder::build('key=value');
        $request = new Request('GET', $uri, ['Content-Type' => 'application/x-www-form-urlencoded'], $body);
        $this->assertEquals(['key' => 'value'], $request->getData());

        // other
        $body = StreamBuilder::build('key=value');
        $request = new Request('GET', $uri, ['Content-Type' => 'text/plain'], $body);
        $this->assertEquals([], $request->getData());
    }

    /**
     * @return array
     *
     * @throws MockObjectException
     */
    private function getMockObjects(): array
    {
        return [
            $this->createMock(Uri::class),
            $this->createMock(Stream::class),
        ];
    }

    /**
     * @return Request
     *
     * @throws MockObjectException
     */
    private function simpleGetRequest(): Request
    {
        [$uri, $body] = $this->getMockObjects();

        return new Request('GET', $uri, ['Content-Type' => 'application/json'], $body);
    }
}