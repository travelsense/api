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

$app->get('/user/finish-registration/{token}', 'controller.user:finishRegisterThroughEmail')
    ->bind('finish-registration');

$app->get('/user', 'controller.user:getUser');


$app->get('/test', function () use ($app) {
    /**
     * @var $fb \Facebook\Facebook
     */
    $fb = $app['facebook'];
    $fb->setDefaultAccessToken('CAAFR0mwpuWMBAIoZAxDA42HfYK8Cb5rXMOmG0AAnZA0QWSUMU6zDQVgOw3QaAGdApRQmyGZCPIyHeDqT5dMSHXFokpQCiHE9LErpA49VrsG5M8k6rXsV8fOOE088s1cPYhZBPkfstRLTDcxJ2XSHcZAM9LkZBgS9YkXzLfPHBlqnmzOVUBwcMw1RkzOuCSeb9ybNNINg3kZBVvIb7R0cJ68');
    $fbUser = $fb->get('/me?fields=email,picture')->getGraphUser();
    var_dump($fbUser->getEmail());
    var_dump($fbUser->getName());
    var_dump($fbUser->getPicture()->getUrl());
    return '';
});