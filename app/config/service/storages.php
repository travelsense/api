<?php
/**
 * System storages
 * @var $app Application
 */

use Api\ExpirableStorage;
use F3\LazyPDO\LazyPDO;

$app['db.main.pdo'] = $app->share(function ($app) {
    $main = $app['config']['db']['main'];
    return new LazyPDO(
        sprintf('%s:host=%s;dbname=%s', $main['driver'], $main['host'], $main['database']),
        $main['user'],
        $main['password'],
        $main['options']
    );
});

$app['storage.expirable_storage'] = $app->share(function($app) {
    return new ExpirableStorage($app['db.main.pdo']);
});
