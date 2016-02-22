<?php
/**
 * Mappers
 * @var $app Application
 */

use Api\ExpirableStorage;
use Api\Mapper\DB\SessionMapper;
use Api\Mapper\DB\TravelMapper;
use Api\Mapper\DB\UserMapper;

$app['mapper.db.user'] = $app->share(function($app) {
    $mapper =  new UserMapper($app['storage.main.pdo']);
    $mapper->setSalt($app['config']['security']['password_salt']);
    return $mapper;
});

$app['mapper.db.sessions'] = $app->share(function($app) {
    return new SessionMapper($app['storage.main.pdo']);
});

$app['mapper.db.expirable_storage'] = $app->share(function($app) {
    return new ExpirableStorage($app['storage.main.pdo']);
});

$app['mapper.db.travel'] = $app->share(function($app) {
    return new TravelMapper($app['storage.main.pdo']);
});
