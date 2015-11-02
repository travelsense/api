<?php
/**
 * Routing
 * @var $app Application
 */

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->post('/user/register-by-email', 'controller.user:startRegisterThroughEmail');
$app->get('/user/finish-registration/{token}', 'controller.user:finishRegisterThroughEmail')
    ->bind('finish-registration');
