<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Guzzle Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure the default settings for Guzzle HTTP client.
    |
    */

    'timeout' => env('GUZZLE_TIMEOUT', 300),
    'connect_timeout' => env('GUZZLE_CONNECT_TIMEOUT', 30),

    'options' => [
        'http_errors' => true,
        'connect_timeout' => env('GUZZLE_CONNECT_TIMEOUT', 30),
        'timeout' => env('GUZZLE_TIMEOUT', 300),
    ],
];
