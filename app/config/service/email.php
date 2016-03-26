<?php
/**
 * Mail service config
 * @var $app Api\Application
 */

use Api\Service\Mailer\MailerService;

$app['mailer'] = $app->share(function($app) {
        $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
        ->setUsername($app['config']['email']['smtp_user'])
        ->setPassword($app['config']['email']['smtp_password']);
    return Swift_Mailer::newInstance($transport);
});

// MailerService
$app['email.service'] = $app->share(function($app) {
    $service = new MailerService($app['mailer'], $app['twig'], $app['config']['email']);
    $service->setLogger($app['monolog']);
    return $service;
});

