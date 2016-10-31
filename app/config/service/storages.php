<?php
/**
 * System storages
 * @var $app Application
 */

use Api\Application;
use Api\ExpirableStorage;
use Api\Migrator\Factory as MigratorFactory;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use LazyPDO\LazyPDO;
use Migrator\VersionLog\DatabaseLog;
use Migrator\VersionLog\DatabaseLogAdapter\Factory as LogAdapterFactory;

$app['db.main.pdo'] = function (Application $app) {
    $main = $app['config']['db']['main'];
    $pdo = new LazyPDO(
        sprintf('%s:host=%s;dbname=%s', $main['driver'], $main['host'], $main['database']),
        $main['user'],
        $main['password'],
        $main['options']
    );
    return $pdo;
};

$app['db.main.connection'] = function (Application $app) {
    $main = $app['config']['db']['main'];

    $config = new Configuration();
    $params = [
        'dbname'   => $main['database'],
        'user'     => $main['user'],
        'password' => $main['password'],
        'host'     => $main['host'],
        'driver'   => 'pdo_pgsql',
    ];
    return DriverManager::getConnection($params, $config);
};

$app['storage.expirable_storage'] = function (Application $app) {
    return new ExpirableStorage($app['db.main.connection']);
};

$app['db.migrator.factory'] = function (Application $app) {
    return new MigratorFactory($app);
};
$app['db.migrator.log'] = function () {
    return new DatabaseLog(
        new LogAdapterFactory('__history')
    );
};
