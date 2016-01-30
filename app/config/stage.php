<?php
return array_replace_recursive(
    require __DIR__ . '/prod.php',
    [
        'log' => [
            'main' => '/var/log/vaca_dev.log',
        ],
    ]
);
