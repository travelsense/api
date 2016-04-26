<?php
/**
 * Controllers
 * @var $app Application
 */

use Api\Controller\AuthController;
use Api\Controller\HealthCheckController;
use Api\Controller\IataController;
use Api\Controller\Travel\CategoriesController;
use Api\Controller\Travel\CommentController;
use Api\Controller\Travel\TravelController;
use Api\Controller\UberController;
use Api\Controller\UserController;
use Api\Controller\WegoHotelController;

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
        $app['mapper.db.travel'],
        $app['mapper.db.category']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
});

$app['controller.categories'] = $app->share(function($app) {
    $controller = new CategoriesController(
        $app['mapper.db.category']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
});

$app['controller.comment'] = $app->share(function($app) {
    $controller = new CommentController(
        $app['mapper.db.comment']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
});

$app['controller.uber'] = $app->share(function($app) {
    return new UberController($app['uber']);
});

$app['controller.wego'] = $app->share(function($app) {
    return new WegoHotelController($app['wego.hotels'], $app['db.main.pdo']);
});

$app['controller.health'] = $app->share(function($app) {
    return new HealthCheckController();
});

$app['controller.iata'] = $app->share(function($app) {
    return new IataController($app['mapper.db.iata']);
});
