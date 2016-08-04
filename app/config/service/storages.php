<?php
/**
 * System storages
 * @var $app Application
 */

use Api\Application;
use Api\ExpirableStorage;
use LazyPDO\LazyPDO;
use Migrator\VersionLog\DatabaseLog;
use Migrator\VersionLog\DatabaseLogAdapter\Factory;

$app['db.main.pdo'] = function (Application $app) {
    $main = $app['config']['db']['main'];
    return new LazyPDO(
        sprintf('%s:host=%s;dbname=%s', $main['driver'], $main['host'], $main['database']),
        $main['user'],
        $main['password'],
        $main['options']
    );
};

$app['storage.expirable_storage'] = function (Application $app) {
    return new ExpirableStorage($app['db.main.pdo']);
};

$app['db.migrator.factory'] = function (Application $app) {
    return new \Api\Migrator\Factory($app);
};
$app['db.migrator.log'] = function (Application $app) {
    return new DatabaseLog(
        new Factory('__history')
    );
};
