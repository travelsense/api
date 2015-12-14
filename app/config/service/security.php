<?php
/**
 * @var $app Application
 */

$app['security.session_manager'] = $app->share(function($app) {
    return new \Security\SessionManager($app['mapper.db.sessions']);
});

// Authentication

$app['auth.enabled'] = $app['config']['security']['enabled'];
$app['auth.unsecured_routes'] = $app['config']['security']['unsecured_routes'];
$app->register(new \Security\Authentication\AuthenticationProvider());
