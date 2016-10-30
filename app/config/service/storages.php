<?php
/**
 * System storages
 * @var $app Application
 */

use Api\Application;
use Api\ExpirableStorage;
use Api\Migrator\Factory as MigratorFactory;
use Api\PDO\LoggingPDO;
use Migrator\VersionLog\DatabaseLog;
use Migrator\VersionLog\DatabaseLogAdapter\Factory as LogAdapterFactory;

$app['db.main.pdo'] = function (Application $app) {
    $main = $app['config']['db']['main'];
    $pdo = new LoggingPDO(
        sprintf('%s:host=%s;dbname=%s', $main['driver'], $main['host'], $main['database']),
        $main['user'],
        $main['password'],
        $main['options']
    );
    $pdo->setLogger($app['logger']);
    return $pdo;
};

$app['storage.expirable_storage'] = function (Application $app) {
    return new ExpirableStorage($app['db.main.pdo']);
};

$app['db.migrator.factory'] = function (Application $app) {
    return new MigratorFactory($app);
};
$app['db.migrator.log'] = function (Application $app) {
    return new DatabaseLog(
        new LogAdapterFactory('__history')
    );
};
