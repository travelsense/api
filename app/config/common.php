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
    ],
    'debug' => false,
    'error_message_mapping' => [
        404 => 'Resource not found',
        500 => 'Internal server error',
        'default' => 'Cthulhu fhtagn!',
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
