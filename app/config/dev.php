<?php
return array_replace_recursive(
    require __DIR__ . '/common.php',
    [
        'debug' => true, //used by error handler
        'log' => [
            'main' => '/tmp/vaca_dev.log',
        ],
        'secure_json' => __DIR__ . '/local.json',
        'storage' => [
            'main' => [
                'user' => 'vaca_dev',
                'database' => 'vaca_dev',
                'password' => 'vaca_dev',
            ],
        ],
    ]
);
