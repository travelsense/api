<?php
/**
 * System storages
 * @var $app Application
 */

use Api\Application;
use Api\ExpirableStorage;
use Api\Migrator\Migrator;
use F3\LazyPDO\LazyPDO;

$app['db.main.pdo'] = $app->share(function (Application $app) {
    $main = $app['config']['db']['main'];
    return new LazyPDO(
        sprintf('%s:host=%s;dbname=%s', $main['driver'], $main['host'], $main['database']),
        $main['user'],
        $main['password'],
        $main['options']
    );
});

$app['db.main.migrator'] = $app->share(function (Application $app) {
    $migrator = new Migrator($app['db.main.pdo'], 'main', $app['config']['migrations']);
    $migrator->init();
    return $migrator;
});

$app['storage.expirable_storage'] = $app->share(function(Application $app) {
    return new ExpirableStorage($app['db.main.pdo']);
});
