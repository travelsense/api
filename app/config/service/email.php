<?php
/**
 * Mail service config
 * @var $app Application
 */


// Swift Mailer
$app['swiftmailer.transport'] = 'Swift_SendmailTransport';
$app->register(new Silex\Provider\SwiftmailerServiceProvider());

// MailerService
$app['email.mailer'] = $app->share(function($app) {
    return new \Service\Mailer\MailerService($app['mailer'], $app['twig'], $app['config']['email']);
});

