<?php
/**
 * Mappers
 * @var $app Application
 */

$app['mapper.db.user'] = $app->share(function($app) {
    $mapper =  new \Mapper\DB\UserMapper($app['storage.main.pdo']);
    $mapper->setSalt($app['config']['security']['password_salt']);
    return $mapper;
});

$app['mapper.db.sessions'] = $app->share(function($app) {
    return new \Mapper\DB\SessionMapper($app['storage.main.pdo']);
});

$app['mapper.db.expirable_storage'] = $app->share(function($app) {
    return new ExpirableStorage($app['storage.main.pdo']);
});

$app['mapper.db.travel'] = $app->share(function($app) {
    return new \Mapper\DB\TravelMapper($app['storage.main.pdo']);
});
