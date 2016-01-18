<?php
/**
 * Routing
 * @var $app Application
 */

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());


// API

$app->post('/user', 'controller.user:createUser')
    ->bind('create-user');

$app->get('/user', 'controller.user:getUser');

$app->post('/token/by-email/{email}', 'controller.user:createTokenByEmail')
    ->bind('login-by-email');

$app->post('/token/by-facebook/{fbToken}', 'controller.user:createTokenByFacebook')
    ->assert('fbToken', '.*') // to allow any characters
    ->convert('fbToken', function ($token) {
        return urldecode($token);
    }) // to convert "+" to " "
    ->bind('login-by-facebook');

$app->get('/travel/{id}/comments', 'controller.travel_comment:getByTravel')
    ->assert('id', '\\d+');


$app->get('/travel/{id}', 'controller.travel:getTravel')
    ->assert('id', '\\d+')
    ->bind('travels-by-id');


// Web

$app->get('/confirm-email/{token}', 'controller.user:confirmEmail')
    ->bind('confirm-email');


$app->get('/change-password', 'controller.user:showPasswordChangeForm')
    ->bind('change-password');
