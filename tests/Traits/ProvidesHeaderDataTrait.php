<?php

declare(strict_types=1);

namespace Loom\HttpComponentTests\Traits;

trait ProvidesHeaderDataTrait
{
    public static function methodProvider(): array
    {
        return [
            'POST' => [
                'method' => 'POST',
            ],
            'PUT' => [
                'method' => 'PUT',
            ],
            'DELETE' => [
                'method' => 'DELETE',
            ],
            'GET' => [
                'method' => 'GET',
            ],
            'PATCH' => [
                'method' => 'PATCH',
            ]
        ];
    }

    public static function headerKeyProvider(): array
    {
        return [
            'Content-Type' => [
                'key' => 'Content-Type',
            ],
            'content-type' => [
                'key' => 'content-type',
            ],
            'CONTENT-TYPE' => [
                'key' => 'CONTENT-TYPE',
            ],
        ];
    }
}