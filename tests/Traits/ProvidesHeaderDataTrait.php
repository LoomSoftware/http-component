<?php

namespace Loom\HttpComponentTests\Traits;

trait ProvidesHeaderDataTrait
{
    /**
     * @return array
     */
    public static function methodProvider(): array
    {
        return [
            'POST' => [
                'POST',
            ],
            'PUT' => [
                'PUT',
            ],
            'DELETE' => [
                'DELETE',
            ],
            'GET' => [
                'GET',
            ],
            'PATCH' => [
                'PATCH',
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