<?php
return array_replace_recursive(
    require __DIR__ . '/prod.php',
    [
        'log' => [
            'main' => [
                'file' => '/tmp/api_stage.log',
                'level' => 'debug',
            ]
        ],
        'application' => [
            'debug' => true,
        ],
        'jobs' => [
            'cron_lock' => '/home/www/cache/job_queue.lock',
            'event_storage_dir' => '/home/www/cache',
        ],
    ]
);
