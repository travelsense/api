#!/bin/env php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = new \Api\Migrator\ConsoleApp(
    \Api\Application::createByEnvironment()
);
$app->run();
