<?php
return array_replace_recursive(
    require __DIR__ . '/common.php',
    [
        'debug' => true, //used by error handler
        'services' => [
            'email' => __DIR__ . '/service/email.mock.php',
        ],
        'log' => [
            'main' => [
                'file' => '/tmp/api_dev.log',
                'level' => 'debug',
            ]
        ],
        'secure_json' => __DIR__ . '/local.json',
        'db' => [
            'main' => [
                'user' => 'api_dev',
                'database' => 'api_dev',
                'password' => 'api_dev',
            ],
        ],
    ]
);
