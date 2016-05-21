<?php
/**
 * Main application config.
 */
return [
    'secure_json' => false,
    'service' => [
        'app'         => __DIR__ . '/service/app.php',
        'controllers' => __DIR__ . '/service/controllers.php',
        'email'       => __DIR__ . '/service/email.php',
        'mappers'     => __DIR__ . '/service/mappers.php',
        'misc'        => __DIR__ . '/service/misc.php',
        'routing'     => __DIR__ . '/service/routing.php',
        'security'    => __DIR__ . '/service/security.php',
        'storages'    => __DIR__ . '/service/storages.php',
        'wego'        => __DIR__ . '/service/wego.php',
    ],
    'debug' => false,
    'log' => [
        'main' => '/var/log/api.log',
    ],
    'error_message_mapping' => [
        404 => 'Resource not found',
        405 => 'Method not allowed',
        500 => 'Internal server error',
        'default' => 'Cthulhu fhtagn!',
    ],
    'email' => [
        'smtp_user' => 'noreply@hoptrip.us',
        'smtp_password' => 'xxx',
        'from_address' => 'noreply@hoptrip.us',
        'from_name' => 'Hoptrip',
        'base_url' => 'https://hoptrip.us',
        'message_log' => '/tmp/email.log', // used in Api\Test\Mailer
    ],
    'security' => [
        'enabled' => true,
        'unsecured_routes' => [
            'change-password',
            'confirm-email',
            'create-token',
            'create-user',
            'health-check',
            'iata-all',
            'iata-by-code',
            'reset-password',
            'send-password-reset-link',
            'travel-by-category',
            'travel-by-id',
            'travel-category',
            'travel-comment',
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
            'user' => 'xxx',
            'database' => 'xxx',
            'password' => 'xxx',
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
