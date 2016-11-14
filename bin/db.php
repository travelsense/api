#!/usr/bin/php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = new \Api\Application();
$app['doctrine.migrations.app.main']->run();
