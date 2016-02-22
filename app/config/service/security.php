<?php
/**
 * @var $app Application
 */

use Api\Security\Authentication\AuthenticationProvider;
use Api\Security\SessionManager;

$app['security.session_manager'] = $app->share(function($app) {
    return new SessionManager($app['mapper.db.sessions']);
});

// Authentication

$app['auth.enabled'] = $app['config']['security']['enabled'];
$app['auth.unsecured_routes'] = $app['config']['security']['unsecured_routes'];
$app->register(new AuthenticationProvider());

// User object

$app['user'] = function ($app) {
    /** @var \Api\Security\Authentication\Credentials $credentials */
    $credentials = $app['auth.credentials'];
    $id = $credentials->getUser();
    if (null === $id) {
        return null;
    }
    /** @var \Api\Mapper\DB\UserMapper $userMapper */
    $userMapper = $app['mapper.db.user'];
    return $userMapper->fetchById($id);
};
