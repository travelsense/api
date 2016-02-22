<?php
/**
 * Mail service config
 * @var $app Application
 */

use Api\Service\Mailer\MailerService;
use Silex\Provider\SwiftmailerServiceProvider;

// Swift Mailer
$app->register(new SwiftmailerServiceProvider());
$app['swiftmailer.transport'] = $app->share(function($app) {
    return Swift_SendmailTransport::newInstance();
});

// MailerService
$app['email.service'] = $app->share(function($app) {
    $mailer = new MailerService($app['mailer'], $app['twig'], $app['config']['email']);
    $mailer->setLogger($app['monolog']);
    return $mailer;
});

