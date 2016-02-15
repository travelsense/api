<?php
/**
 * Controllers
 * @var $app Application
 */

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

// API

$app['controller.user'] = $app->share(function($app) {
    $controller = new Controller\UserController(
        $app['mapper.db.user'],
        $app['email.service'],
        $app['storage.expirable_storage'],
        $app['password_generator']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
});

$app['controller.auth'] = $app->share(function($app) {
    $controller = new Controller\AuthController(
        $app['mapper.db.user'],
        $app['security.session_manager'],
        $app['facebook'],
        $app['password_generator']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
});

$app['controller.travel'] = $app->share(function($app) {
    $controller = new Controller\TravelController(
        $app['mapper.db.travel']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
});

$app['controller.uber'] = $app->share(function($app) {
    return new Controller\UberController($app['uber']);
});

$app['controller.wego'] = $app->share(function($app) {
    return new Controller\WegoController($app['wego']);
});
