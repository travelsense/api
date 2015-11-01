<?php
/**
 * Email service config
 * @var $app Application
 */

$app['email.mandrill'] = $app->share(function($app) {
    return new Mandrill($app['email']['mandrill']['key']);
});