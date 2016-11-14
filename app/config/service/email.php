<?php
/**
 * Mail service config
 * @var $app Api\Application
 */

use Api\Service\Mailer;

$app['mailer'] = function ($app) {
    $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
        ->setUsername($app['config']['email']['smtp_user'])
        ->setPassword($app['config']['email']['smtp_password']);
    return Swift_Mailer::newInstance($transport);
};

// MailerService
$app['email.service'] = function ($app) {
    $service = new Mailer($app['mailer'], $app['twig'], $app['pdf_generator'], $app['config']['email']);
    $service->setLogger($app['monolog']);
    return $service;
};
