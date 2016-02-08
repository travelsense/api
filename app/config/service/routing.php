<?php
/**
 * Routing
 * @var $app Application
 */

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());


// API

$app->post('/user', 'controller.api.user:createUser')
    ->bind('create-user');

$app->get('/user', 'controller.api.user:getUser');

$app->post('/token/by-email/{email}', 'controller.api.auth:createTokenByEmail')
    ->bind('login-by-email');

$app->post('/token/by-facebook/{fbToken}', 'controller.api.auth:createTokenByFacebook')
    ->assert('fbToken', '.*') // to allow any characters
    ->convert('fbToken', 'urldecode') // to convert "+" to " "
    ->bind('login-by-facebook');

$app->get('/uber/price/{lat1}/{lon1}/{lat2}/{lon2}', 'controller.api.uber:getPriceEstimate');

$app->post('/hotel/search-results/{id}/{page}')
    ->convert('in', $app['converter.date'])
    ->convert('out', $app['converter.date'])
    ->convert('rooms', 'intval');

$app->post('/hotel/search/{location}/{in}/{out}/{rooms}', 'controller.api.wego')
    ->convert('in', $app['converter.date'])
    ->convert('out', $app['converter.date'])
    ->convert('rooms', 'intval');

// Web

$app->get('/confirm-email/{token}', 'controller.user:confirmEmail')
    ->bind('confirm-email');


$app->get('/change-password', 'controller.user:showPasswordChangeForm')
    ->bind('change-password');
