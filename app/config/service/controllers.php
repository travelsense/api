<?php
/**
 * Controllers
 * @var $app Application
 */

use Api\Controller\AuthController;
use Api\Controller\TravelController;
use Api\Controller\UberController;
use Api\Controller\UserController;
use Api\Controller\WegoController;
use Api\Controller\HealthCheckController;

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

// API

$app['controller.user'] = $app->share(function($app) {
    $controller = new UserController(
        $app['mapper.db.user'],
        $app['email.service'],
        $app['storage.expirable_storage'],
        $app['password_generator']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
});

$app['controller.auth'] = $app->share(function($app) {
    $controller = new AuthController(
        $app['mapper.db.user'],
        $app['security.session_manager'],
        $app['facebook'],
        $app['password_generator']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
});

$app['controller.travel'] = $app->share(function($app) {
    $controller = new TravelController(
        $app['mapper.db.travel']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
});

$app['controller.uber'] = $app->share(function($app) {
    return new UberController($app['uber']);
});

$app['controller.wego'] = $app->share(function($app) {
    return new WegoController($app['wego']);
});

$app['controller.health'] = $app->share(function($app) {
    return new HealthCheckController();
});
