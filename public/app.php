<?php
if (preg_match('/\.(?:html|js|png|jpg|jpeg|gif|ico|css|woff|svg|eot)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is.
}
require_once __DIR__.'/../vendor/autoload.php';
Symfony\Component\Debug\ErrorHandler::register(); //converts errors to exceptions
$env = getenv('APP_ENV') ?: 'prod';
$app = Application::createByEnvironment($env);
$app->run();