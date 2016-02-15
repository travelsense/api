<?php
/**
 * Mail service config
 * @var $app Application
 */


// Swift Mailer
$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app['swiftmailer.transport'] = $app->share(function($app) {
    return Swift_SendmailTransport::newInstance();
});

// MailerService
$app['email.service'] = $app->share(function($app) {
    $mailer = new \Service\Mailer\MailerService($app['mailer'], $app['twig'], $app['config']['email']);
    $mailer->setLogger($app['monolog']);
    return $mailer;
});

