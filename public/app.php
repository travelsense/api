<?php
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . preg_replace('/(\?.*)$/', '', $_SERVER['REQUEST_URI']))) {
    return false;
}
require_once __DIR__.'/../vendor/autoload.php';
(new Api\Application())->run();
