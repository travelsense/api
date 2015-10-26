<?php
require_once __DIR__.'/../vendor/autoload.php';
$env = getenv('APP_ENV') ?: 'prod';
$app = new Application(require sprintf(__DIR__.'/../app/config/%s.php', $env));
$app->run();