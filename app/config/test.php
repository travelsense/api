<?php
return array_replace_recursive(
    require __DIR__ . '/common.php',
    [
        'debug' => true,
        'service' => [
            'email' => __DIR__ . '/service/email.mock.php',
        ],
        'log' => [
            'main' => '/tmp/vaca_test.log',
        ],
        'db' => [
            'main' => [
                'user'     => 'vaca_test',
                'database' => 'vaca_test',
                'password' => 'vaca_test',
            ],
        ],
    ]
);
