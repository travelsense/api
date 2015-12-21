<?php
require_once __DIR__.'/../vendor/autoload.php';
Symfony\Component\Debug\ErrorHandler::register(); //converts errors to exceptions
$env = getenv('APP_ENV') ?: 'prod';
$app = Application::createByEnvironment($env);
$app->run();