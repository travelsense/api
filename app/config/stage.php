<?php
return array_replace_recursive(
    require __DIR__ . '/prod.php',
    [
        'log' => [
            'main' => [
                'file'  => '/tmp/api_stage.log',
                'level' => 'debug',
            ],
        ],
    ]
);
