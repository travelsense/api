<?php
/**
 * Main application config.
 *
 * WARNING! Mind the internal keys in Silex\Application
 */
return [
    'secure_json' => false,
    'services' => [
        __DIR__.'/services/storage.php',
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
