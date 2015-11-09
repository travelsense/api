<?php
/**
 * Mappers
 * @var $app Application
 */

$app['mapper.user'] = $app->share(function($app) {
    $mapper =  new \Mapper\UserMapper($app['storage.main.pdo']);
    $mapper->setSalt($app['config']['security']['password_salt']);
    return $mapper;
});

$app['mapper.sessions'] = $app->share(function($app) {
   return new \Mapper\SessionMapper($app['storage.main.pdo']);
});