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

$app->register(new \Silex\Provider\DoctrineServiceProvider(), [
    'dbs.options' => [
        'main' => [
            'dbname'   => $app['config']['db']['main']['database'],
            'user'     => $app['config']['db']['main']['user'],
            'password' => $app['config']['db']['main']['password'],
            'host'     => $app['config']['db']['main']['host'],
            'driver'   => 'pdo_pgsql',
        ],
    ],
]);


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

$app['storage.expirable_storage'] = function (Application $app) {
    return new ExpirableStorage($app['dbs']['main']);
};

$app['db.migrator.factory'] = function (Application $app) {
    return new MigratorFactory($app);
};
$app['db.migrator.log'] = function () {
    return new DatabaseLog(
        new LogAdapterFactory('__history')
    );
};
