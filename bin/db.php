#!/usr/bin/php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = new \Api\Migrator\App();
$app->run();
