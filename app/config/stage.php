<?php
return array_merge_recursive(
    require __DIR__ . '/common.php',
    [
        'application' => [
            'debug' => true,
        ],
        'secure_json' => '/etc/secure.json',
        'image_upload' => [
            'dir' => '/www/images',
            'base_url' => 'https://static.hoptrip.us',
        ],
        'email' => [
            'booking_details_receivers' => ['karapetov@gmail.com', 'book@hoptrip.us'],
            'stats_receivers'           => ['karapetov@gmail.com'],
        ],
        'log' => [
            'main' => [
                'file' => '/tmp/api_stage.log',
                'level' => 'debug',
            ]
        ],
        'jobs' => [
            'cron_lock' => '/home/www/cache/job_queue.lock',
            'event_storage_dir' => '/home/www/cache',
        ],
    ]
);
