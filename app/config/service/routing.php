<?php
/**
 * Routing
 * @var $app Application
 */

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->post('/user/register-by-email', 'controller.user:startRegisterThroughEmail')
    ->bind('register-by-email');

$app->post('/user/login-by-email', 'controller.user:loginByEmailAndPassword')
    ->bind('login-by-email');

$app->post('/user/login-by-facebook', 'controller.user:loginByFacebook')
    ->bind('login-by-facebook');

$app->get('/user/finish-registration/{token}', 'controller.user:finishRegisterThroughEmail')
    ->bind('finish-registration');

$app->get('/user', 'controller.user:getUser');
