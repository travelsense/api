<?php
/**
 * Routing
 * @var $app Application
 */

use Silex\Provider\UrlGeneratorServiceProvider;

$app->register(new UrlGeneratorServiceProvider());

// Parameter converters

$toDate = function ($date) {
    return new DateTime($date);
};

$toInt = function ($val) {
    return intval($val);
};

$iataType = '^country|city|port|carrier$';

$app->get('/healthCheck', 'controller.health:healthCheck')
    ->bind('health-check');

$app->post('/user', 'controller.user:createUser')
    ->bind('create-user');

$app->get('/user', 'controller.user:getUser');

$app->post('/email/confirm/{token}', 'controller.user:confirmEmail')
    ->bind('confirm-email');

$app->post('/password/reset/{token}', 'controller.user:resetPassword')
    ->bind('reset-password');

$app->post('/password/link/{email}', 'controller.user:sendPasswordResetLink')
    ->bind('send-password-reset-link');

$app->post('/token', 'controller.auth:create')
    ->bind('create-token');

$app->get('/uber/price/{lat1}/{lon1}/{lat2}/{lon2}', 'controller.uber:getPriceEstimate');

$app->post('/travel', 'controller.travel:createTravel');

$app->get('/travel/{id}', 'controller.travel:getTravel')
    ->convert('id', $toInt)
    ->bind('travel-by-id');

$app->get('/iata/{type}/code/{code}', 'controller.iata:getOne')
    ->assert('type', $iataType)
    ->bind('iata-by-code');

$app->get('/iata/{type}/all', 'controller.iata:getAll')
    ->assert('type', $iataType)
    ->bind('iata-all');

$app->post('/hotel/search/{location}/{in}/{out}/{rooms}', 'controller.wego:startSearch')
    ->convert('in', $toDate)
    ->convert('out', $toDate)
    ->convert('rooms', $toInt);

$app->get('/hotel/search-results/{id}/{page}', 'controller.wego:getSearchResults')
    ->convert('page', $toInt);
