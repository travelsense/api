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

$app->get('/travel/{id}', 'controller.travel:getTravel')
    ->bind('travel-by-id');

$app->post('/hotel/search/{location}/{in}/{out}/{rooms}', 'controller.wego:startSearch')
    ->convert('in', $toDate)
    ->convert('out', $toDate)
    ->convert('rooms', $toInt);

$app->get('/hotel/search-results/{id}/{page}', 'controller.wego:getSearchResults')
    ->convert('page', $toInt);

$app->put('/user', 'controller.user:update');
