<?php
return array_replace_recursive(
    require __DIR__.'/common.php',
    [
        'debug' => true, //used by error handler
        'storage' => [
            'main' => [
                'driver' => 'pgsql',
                'user' => 'vaca',
                'database' => 'vaca_test',
                'password' => 'vaca',
            ]
        ]
    ]
);