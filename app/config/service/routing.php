<?php
/**
 * Routing
 * @var $app Application
 */

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// Parameter converters

$toDate = function ($date) {
    return new DateTime($date);
};

$toInt = function ($val) {
    return intval($val);
};

// API

$app->post('/user', 'controller.api.user:createUser')
    ->bind('create-user');

$app->get('/user', 'controller.api.user:getUser');

$app->post('/token/by-email/{email}', 'controller.api.auth:createTokenByEmail')
    ->bind('login-by-email');

$app->post('/token/by-facebook/{fbToken}', 'controller.api.auth:createTokenByFacebook')
    ->assert('fbToken', '.*') // to allow any characters
    ->convert('fbToken', function ($val) {
        return urldecode($val);
    }) // to convert "+" to " "
    ->bind('login-by-facebook');

$app->get('/uber/price/{lat1}/{lon1}/{lat2}/{lon2}', 'controller.api.uber:getPriceEstimate');

$app->post('/hotel/search/{location}/{in}/{out}/{rooms}', 'controller.api.wego:startSearch')
    ->convert('in', $toDate)
    ->convert('out', $toDate)
    ->convert('rooms', $toInt);

$app->get('/hotel/search-results/{id}/{page}', 'controller.api.wego:getSearchResults')
    ->convert('page', $toInt);

// Web

$app->get('/confirm-email/{token}', 'controller.user:confirmEmail')
    ->bind('confirm-email');


$app->get('/change-password', 'controller.user:showPasswordChangeForm')
    ->bind('change-password');
