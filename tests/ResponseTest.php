<?php

declare(strict_types=1);

namespace Loom\HttpComponentTests;

use Loom\HttpComponent\Response;
use Loom\HttpComponentTests\Traits\ProvidesHeaderDataTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    use ProvidesHeaderDataTrait;

    public function testGetProtocolVersion(): void
    {
        $this->assertEquals('1.1', (new Response())->getProtocolVersion());
    }

    public function testWithProtocolVersion(): void
    {
        $response = new Response();
        $response = $response->withProtocolVersion('2.0');

        $this->assertEquals('2.0', $response->getProtocolVersion());
    }

    public function testGetHeaders(): void
    {
        $this->assertEquals(['content-type' => ['application/json']], ($this->getSimpleResponse())->getHeaders());
    }

    #[DataProvider('headerKeyProvider')]
    public function testHasHeader(string $key): void
    {
        $this->assertTrue(($this->getSimpleResponse())->hasHeader($key));
    }

    public function testGetHeader(): void
    {
        $this->assertEquals(['application/json'], ($this->getSimpleResponse())->getHeader('CONTENT-TYPE'));
    }

    public function testWithHeader(): void
    {
        $response = $this->getSimpleResponse();
        $response = $response->withHeader('content-type', 'text/html');

        $this->assertEquals(['text/html'], $response->getHeader('content-type'));
    }

    public function testWithAddedHeader(): void
    {
        $response = $this->getSimpleResponse();
        $response = $response->withAddedHeader('content-type', 'text/html');

        $this->assertEquals(['application/json', 'text/html'], $response->getHeader('content-type'));
    }

    public function testGetHeaderLine(): void
    {
        $response = $this->getSimpleResponse();
        $response = $response->withAddedHeader('content-type', 'text/html');

        $this->assertEquals('application/json, text/html', $response->getHeaderLine('content-type'));
    }

    public function testWithoutHeader(): void
    {
        $response = $this->getSimpleResponse();
        $response = $response->withoutHeader('CONTENT-TYPE');

        $this->assertEquals([], $response->getHeaders());
    }

    public function testWithStatus(): void
    {
        $response = $this->getSimpleResponse();
        $response = $response->withStatus(404);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testGetReasonPhrase(): void
    {
        $response = $this->getSimpleResponse();

        $this->assertEquals('OK', $response->getReasonPhrase());
    }

    private function getSimpleResponse(): Response
    {
        return new Response(200, 'OK', ['Content-Type' => 'application/json']);
    }
}