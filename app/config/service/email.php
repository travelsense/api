<?php
/**
 * Email service config
 * @var $app Application
 */

$app['email.mandrill'] = $app->share(function($app) {
    return new Mandrill($app['config']['email']['mandrill']['key']);
});

$app['email.mandrill.messages'] = $app->share(function($app) {
    return $app['email.mandrill']->messages;
});

$app['email.mailer'] = $app->share(function ($app) {
   return new Service\Mailer\MailerService($app['email.mandrill.messages'], $app['twig']);
});