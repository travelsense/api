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
    ->convert('fbToken', function ($token) {
        return urldecode($token);
    }) // to convert "+" to " "
    ->bind('login-by-facebook');

$app->get('/cab/{lat1}/{lon1}/{lat2}/{lon2}', 'controller.api.uber:getPriceEstimate');


// Web

$app->get('/confirm-email/{token}', 'controller.user:confirmEmail')
    ->bind('confirm-email');


$app->get('/change-password', 'controller.user:showPasswordChangeForm')
    ->bind('change-password');
