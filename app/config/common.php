<?php
/**
 * Main application config.
 */
return [
    'secure_json' => false,
    'service' => [
        __DIR__ . '/service/app.php', // must be processed first
        __DIR__ . '/service/controllers.php',
        __DIR__ . '/service/email.php',
        __DIR__ . '/service/mappers.php',
        __DIR__ . '/service/misc.php',
        __DIR__ . '/service/routing.php',
        __DIR__ . '/service/security.php',
        __DIR__ . '/service/storages.php',
        __DIR__ . '/service/validator.php',
        __DIR__ . '/service/wego.php',
    ],
    'debug' => false,
    'log' => [
        'main' => [
            'file' => '/var/log/api.log',
            'level' => 'info',
        ],
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
            'version',
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
    'booking' => [
        'reward_point_price' => 0.1, // 10 cents
    ],
    'schema_path' =>'/../../app/json-schema/',
];
