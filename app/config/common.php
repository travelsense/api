<?php
/**
 * Main application config.
 *
 * WARNING! Mind the internal keys in Silex\Application
 */
return [
    'secure_json' => false,
    'services' => [
        __DIR__.'/services/app.php',
        __DIR__.'/services/storages.php',
        __DIR__.'/services/controllers.php',
        __DIR__.'/services/mailer.php',
    ],
    'debug' => false,
    'error_message_mapping' => [
        404 => 'Resource not found',
        500 => 'Internal server error',
        'default' => 'Cthulhu fhtagn!',
    ],
    'email' => [
        'mandrill' => [
            'key' => '4JbhVtLpBIUqy3QuiFZGSw',
        ]
    ],
    'storage' => [
        'main' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'user' => 'vacarious',
            'database' => 'vacarious',
            'password' => 'vacarious',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ],
        ],
    ],
];
