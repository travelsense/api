<?php
if (preg_match('/\.(?:html|js|png|jpg|jpeg|gif|ico|css|woff|svg|eot)$/', $_SERVER["REQUEST_URI"])) {
    return false;    // serve the requested resource as-is.
}
require_once __DIR__.'/../vendor/autoload.php';
$app = Application::createByEnvironment('dev');
$app['mailer'] = $app->share(function($app) {
   return new Test\Mailer('/tmp/mail.log');
});
$app->run();
