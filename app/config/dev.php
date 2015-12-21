<?php
return array_replace_recursive(
    require __DIR__.'/common.php',
    [
        'debug' => true, //used by error handler
        'secure_json' => __DIR__.'/local.json',
    ]
);
