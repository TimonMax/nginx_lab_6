<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class ClientFactory
{
    public static function make(string $baseUri): Client
    {
        return new Client([
            'base_uri' => $baseUri,
            'timeout' => 5.0,
            'connect_timeout' => 3.0,
            'http_errors' => false,
            'auth' => [CLICKHOUSE_USER, CLICKHOUSE_PASSWORD],
        ]);
    }
}