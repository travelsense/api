<?php
/**
 * Controllers
 * @var $app Application
 */

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app['controller.activity'] = $app->share(function($app) {
    return new Controller\Activity($app['storage.main.pdo']);
});

$app['controller.user'] = $app->share(function($app) {
    $secureToken = new SecureToken($app['secure_token_key']);
    return new Controller\UserController($app['mapper.user'], $app['email.mailer'], $secureToken);
});

$app['controller.test'] = $app->share(function($app) {
    return new Controller\Test();
});