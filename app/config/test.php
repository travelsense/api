<?php
return array_replace_recursive(
    require __DIR__ . '/common.php',
    [
        'debug' => true,
        'service' => [
            'email' => 'email.mock.php',
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
        'email' => [
            'email_confirm' => 'https://example.com/email/confirm/',
            'password_reset' => 'https://example.com/password/reset/',
        ],
    ]
);
