<?php
return array_replace_recursive(
    require __DIR__ . '/common.php',
    [
        'debug' => true,
        'service' => [
            'email' => __DIR__ . '/service/email.mock.php',
        ],
        'log' => [
            'main' => [
                'file' => '/tmp/api_test.log',
                'level' => 'debug',
            ]
        ],
        'db' => [
            'main' => [
                'user'     => 'api_test',
                'database' => 'api_test',
                'password' => 'api_test',
            ],
        ],
    ]
);
