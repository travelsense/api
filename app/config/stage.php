<?php
return array_replace_recursive(
    require __DIR__ . '/prod.php',
    [
        'log' => [
            'main' => '/var/log/api_stage.log',
        ],
    ]
);
