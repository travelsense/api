<?php
return array_replace_recursive(
    require __DIR__.'/common.php',
    [
        'secure_json' => '/etc/secure.json',
        'image_upload' => [
            'dir' => '/www/images',
            'base_url' => 'https://static.hoptrip.us',
        ],
        'email' => [
            'booking_details_receivers' => ['karapetov@gmail.com', 'book@hoptrip.us']
        ],
        'jobs' => [
            'cron_lock' => '/www/cache/job_queue.lock',
            'event_storage_dir' => '/www/cache',
        ],
    ]
);
