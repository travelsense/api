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

$app->post('/user', 'controller.user:createUser')
    ->bind('create-user');

$app->get('/user', 'controller.user:getUser');

$app->post('/email/confirm/{token}', 'controller.user:confirmEmail')
    ->bind('confirm-email');

$app->post('/password/reset/{token}', 'controller.user:resetPassword')
    ->bind('reset-password');

$app->post('/password/link/{email}', 'controller.user:sendPasswordResetLink')
    ->bind('send-password-reset-link');

$app->post('/token/by-email/{email}', 'controller.auth:createTokenByEmail')
    ->bind('login-by-email');

$app->post('/token/by-facebook/{fbToken}', 'controller.auth:createTokenByFacebook')
    ->assert('fbToken', '.*') // to allow any characters
    ->convert('fbToken', function ($val) {
        return urldecode($val);
    }) // to convert "+" to " "
    ->bind('login-by-facebook');

$app->get('/uber/price/{lat1}/{lon1}/{lat2}/{lon2}', 'controller.uber:getPriceEstimate');

$app->post('/hotel/search/{location}/{in}/{out}/{rooms}', 'controller.wego:startSearch')
    ->convert('in', $toDate)
    ->convert('out', $toDate)
    ->convert('rooms', $toInt);

$app->get('/hotel/search-results/{id}/{page}', 'controller.wego:getSearchResults')
    ->convert('page', $toInt);
