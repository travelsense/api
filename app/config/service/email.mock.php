<?php
/**
 * Mail service config
 * @var $app Api\Application
 */
require __DIR__ . '/email.php';

use Api\Test\Mailer;

$app['mailer'] = $app->share(function($app) {
    return new Mailer($app['config']['email']['message_log']);
});
