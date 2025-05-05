<?php

declare(strict_types=1);

namespace Loom\HttpComponent\Traits;

trait ResolveHeadersTrait
{
    protected function setHeaders(array $headers): array
    {
        $sortedHeaders = [];

        foreach ($headers as $key => $header) {
            $sortedHeaders[strtolower($key)] = is_array($header) ? $header : [$header];
        }

        return $sortedHeaders;
    }
}