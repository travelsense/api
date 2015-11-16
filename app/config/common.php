<?php
/**
 * Main application config.
 *
 * WARNING! Mind the internal keys in Silex\Application
 */
return [
    'secure_json' => false,
    'service' => [
        __DIR__.'/service/app.php',
        __DIR__.'/service/security.php',
        __DIR__.'/service/storages.php',
        __DIR__.'/service/mappers.php',
        __DIR__.'/service/controllers.php',
        __DIR__.'/service/routing.php',
        __DIR__.'/service/email.php',
        __DIR__.'/service/misc.php',
    ],
    'debug' => false,
    'error_message_mapping' => [
        404 => 'Resource not found',
        500 => 'Internal server error',
        'default' => 'Cthulhu fhtagn!',
    ],
    'email' => [
        'mandrill' => [
            'key' => 'xxx',
        ]
    ],
    'security' => [
        'enabled' => true,
        'token_key' => '12341234123412341234123412341234',
        'unsecured_routes' => [
            'register-by-email',
            'finish-registration',
            'login-by-email',
            'login-by-facebook',
        ],
        'password_salt' => 'oquaezooQuoo9Iex8haht9thewaa2Sae',
    ],
    'facebook' => [
        'app_id' => 'xxx',
        'app_secret' => 'xxx',
        'default_graph_version' => 'v2.5',
    ],
    'storage' => [
        'main' => [
            'driver' => 'pgsql',
            'host' => 'localhost',
            'user' => 'vaca',
            'database' => 'vaca',
            'password' => 'vaca',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ],
        ],
    ],
];
