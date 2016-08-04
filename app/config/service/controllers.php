<?php
/**
 * Controllers
 * @var $app Api\Application
 */

use Api\ArgumentValueResolver;
use Api\Controller\AuthController;
use Api\Controller\ClientController;
use Api\Controller\HealthCheckController;
use Api\Controller\IataController;
use Api\Controller\Travel\BookingController;
use Api\Controller\Travel\CategoriesController;
use Api\Controller\Travel\CommentController;
use Api\Controller\Travel\TravelController;
use Api\Controller\UberController;
use Api\Controller\UserController;
use Api\Controller\WegoHotelController;

$app['argument_value_resolvers'] = $app->extend('argument_value_resolvers', function (array $resolvers, $app) {
    return array_merge(
        $resolvers,
        [
            new ArgumentValueResolver($app),
        ]
    );
});

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

// API

$app['controller.user'] = function ($app) {
    $controller = new UserController(
        $app['mapper.db.user'],
        $app['email.service'],
        $app['storage.expirable_storage'],
        $app['password_generator']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
};

$app['controller.auth'] = function ($app) {
    $controller = new AuthController(
        $app['mapper.db.user'],
        $app['security.session_manager'],
        $app['facebook'],
        $app['password_generator']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
};

$app['controller.travel'] = function ($app) {
    $controller = new TravelController(
        $app['mapper.db.travel'],
        $app['mapper.db.category'],
        $app['mapper.db.action']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
};

$app['controller.categories'] = function ($app) {
    $controller = new CategoriesController(
        $app['mapper.db.category']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
};

$app['controller.comment'] = function ($app) {
    $controller = new CommentController(
        $app['mapper.db.comment']
    );
    $controller->setLogger($app['monolog']);
    return $controller;
};

$app['controller.uber'] = function ($app) {
    return new UberController($app['uber']);
};

$app['controller.wego'] = function ($app) {
    return new WegoHotelController($app['wego.hotels'], $app['db.main.pdo']);
};

$app['controller.health'] = function ($app) {
    return new HealthCheckController($app['db.main.migrator']);
};

$app['controller.iata'] = function ($app) {
    return new IataController($app['mapper.db.iata']);
};

$app['controller.client'] = function ($app) {
    return new ClientController();
};

$app['controller.booking'] = function ($app) {
    $controller = new BookingController($app['mapper.db.booking']);
    $controller->setPointPrice($app['config']['booking']['reward_point_price']);
    return $controller;
};
