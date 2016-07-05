<?php
return array_replace_recursive(
    require __DIR__.'/common.php',
    [
        'secure_json' => '/etc/secure.json',
        'log' => [
            'main' => [
                'file' => '/tmp/api_dev.log',
                'level' => 'warning',
            ]
        ],
    ]
);
