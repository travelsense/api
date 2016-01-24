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

// User object

$app['user'] = function ($app) {
    /** @var \Security\Authentication\Credentials $credentials */
    $credentials = $app['auth.credentials'];
    $id = $credentials->getUser();
    if (null === $id) {
        return null;
    }
    /** @var \Mapper\DB\UserMapper $userMapper */
    $userMapper = $app['mapper.db.user'];
    return $userMapper->fetchById($id);
};