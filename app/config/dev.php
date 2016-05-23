<?php
return array_replace_recursive(
    require __DIR__ . '/common.php',
    [
        'debug' => true, //used by error handler
        'service' => [
            'email'       => __DIR__ . '/service/email.mock.php',
        ],
        'log' => [
            'main' => '/tmp/api_dev.log',
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
