<?php
return array_replace_recursive(
    require __DIR__.'/common.php',
    [
        'secure_json' => '/etc/secure.json',
        'image_upload' => [
            'dir' => '/www/images',
            'base_url' => 'https://static.hoptrip.us',
        ],
    ]
);
