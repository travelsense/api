<?php
return array_replace_recursive(
    require __DIR__.'/common.php',
    [
        'secure_json' => '/etc/vacarious/secure.json',
        'email' => [
            'mandrill' => [
                'key' => '4JbhVtLpBIUqy3QuiFZGSw', // TODO move to secure json
            ]
        ],
    ]
);
