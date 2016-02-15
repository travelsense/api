<?php
/**
 * Controllers
 * @var $app Application
 */

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

// API

$app['controller.api.user'] = $app->share(function($app) {
    $controller = new Controller\Api\UserController(
        $app['mapper.db.user'],
        $app['email.service'],
        $app['storage.expirable_storage'],
        $app['password_generator']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
});

$app['controller.api.auth'] = $app->share(function($app) {
    $controller = new Controller\Api\AuthController(
        $app['mapper.db.user'],
        $app['security.session_manager'],
        $app['facebook'],
        $app['password_generator']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
});

$app['controller.api.travel'] = $app->share(function($app) {
    $controller = new Controller\Api\TravelController(
        $app['mapper.db.travel']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
});

$app['controller.api.uber'] = $app->share(function($app) {
    return new Controller\Api\UberController($app['uber']);
});

$app['controller.api.wego'] = $app->share(function($app) {
    return new Controller\Api\WegoController($app['wego']);
});

// Web

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
