<?php
/**
 * Main application config.
 *
 * WARNING! Mind the internal keys in Silex\Application
 */
return [
    'secure_json' => false,
    'service' => [
        __DIR__ . '/service/app.php',
        __DIR__ . '/service/controllers.php',
        __DIR__ . '/service/email.php',
        __DIR__ . '/service/mappers.php',
        __DIR__ . '/service/misc.php',
        __DIR__ . '/service/routing.php',
        __DIR__ . '/service/security.php',
        __DIR__ . '/service/storages.php',
    ],
    'debug' => false,
    'log' => [
        'main' => '/var/log/vaca.log',
    ],
    'error_message_mapping' => [
        404 => 'Resource not found',
        405 => 'Method not allowed',
        500 => 'Internal server error',
        'default' => 'Cthulhu fhtagn!',
    ],
    'email' => [
        'from_address' => 'robot@travelsen.se',
        'from_name' => 'Travelsen.se',
        'base_url' => 'https://travelnsen.se',
    ],
    'security' => [
        'enabled' => true,
        'unsecured_routes' => [
            'change-password',
            'confirm-email',
            'create-token',
            'create-user',
            'health-check',
            'reset-password',
            'send-password-reset-link',
            'travel-by-id',
        ],
        'password_salt' => 'oquaezooQuoo9Iex8haht9thewaa2Sae',
    ],
    'facebook' => [
        'app_id' => 'xxx',
        'app_secret' => 'xxx',
        'default_graph_version' => 'v2.5',
    ],
    'db' => [
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
    'migrations' => __DIR__ . '/../../db',
    'uber' => [
        'server_token' => 'xxx',
    ],
    'wego' => [
        'key' => 'xxx',
        'ts_code' => 'xxx',
    ],
];
