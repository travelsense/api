<?php
/**
 * Main application config.
 */
return [
    'secure_json' => false,
    'services' => [
        'app'         => __DIR__ . '/service/app.php',
        'controllers' => __DIR__ . '/service/controllers.php',
        'doctrine'    => __DIR__ . '/service/doctrine.php',
        'email'       => __DIR__ . '/service/email.php',
        'events'      => __DIR__ . '/service/events.php',
        'mappers'     => __DIR__ . '/service/mappers.php',
        'misc'        => __DIR__ . '/service/misc.php',
        'routing'     => __DIR__ . '/service/routing.php',
        'security'    => __DIR__ . '/service/security.php',
        'wego'        => __DIR__ . '/service/wego.php',
    ],
    'application' => [
        'debug' => false,
    ],
    'log' => [
        'main' => [
            'file' => '/var/log/api.log',
            'level' => 'info',
        ],
    ],
    'email' => [
        'smtp_user'                 => 'noreply@hoptrip.us',
        'smtp_password'             => 'xxx',
        'from_address'              => 'noreply@hoptrip.us',
        'from_name'                 => 'Hoptrip',
        'base_url'                  => 'https://hoptrip.us',
        'message_log'               => '/tmp/email.log', // used in Api\Test\Mailer
        'email_confirm'             => 'https://hoptrip.us/email/confirm/%s',
        'password_reset'            => 'https://hoptrip.us/password/reset/%s',
        'booking_details_receivers' => [],
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
            'travel-by-category',
            'travel-by-id',
            'travel-published-by-author',
            'travel-search',
            'travel-category',
            'travel-category-new',
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
            'driver'   => 'pgsql',
            'host'     => 'localhost',
            'user'     => 'xxx',
            'database' => 'xxx',
            'password' => 'xxx',
        ],
    ],
    'migrations' => __DIR__ . '/../../db',
    'uber' => [
        'server_token' => 'xxx',
    ],
    'wego' => [
        'key'     => 'xxx',
        'ts_code' => 'xxx',
    ],
    'booking' => [
        'reward_point_price' => 0.1, // 10 cents
    ],
    'pdf_generator' => [
        'permissions' => ['copy', 'print', 'extract', 'print-highres'],
        'password'    => 'hoptrip',
        'key_length'  => 128,
    ],
    'image_upload' => [
        'dir' => '/tmp/images',
        'base_url' => 'https://static.hoptrip.us',
        'allowed_mime_types' => [
            'image/jpeg',
        ],
        'size_limit' => 4 * 1024 * 1024,
    ],
    'image_copier' => [
        'timeout' => 5,
    ],
];
