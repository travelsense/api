<?php
/**
 * System storages
 * @var $app Application
 */

use Api\Application;
use Api\ExpirableStorage;
use Api\Migrator\Migrator;
use LazyPDO\LazyPDO;

$app['db.main.pdo'] = function (Application $app) {
    $main = $app['config']['db']['main'];
    return new LazyPDO(
        sprintf('%s:host=%s;dbname=%s', $main['driver'], $main['host'], $main['database']),
        $main['user'],
        $main['password'],
        $main['options']
    );
};

$app['db.main.migrator'] = function (Application $app) {
    $migrator = new Migrator($app['db.main.pdo'], 'main', $app['config']['migrations']);
    $migrator->init();
    return $migrator;
};

$app['storage.expirable_storage'] = function (Application $app) {
    return new ExpirableStorage($app['db.main.pdo']);
};
