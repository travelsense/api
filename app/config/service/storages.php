<?php
/**
 * System storages
 * @var $app Application
 */

$app['storage.main.pdo'] = $app->share(function ($app) {
    $main = $app['config']['storage']['main'];
    return new F3\LazyPDO\LazyPDO(
        sprintf('%s:host=%s;dbname=%s', $main['driver'], $main['host'], $main['database']),
        $main['user'],
        $main['password'],
        $main['options']
    );
});

$app['storage.expirable_storage'] = $app->share(function($app) {
    return new ExpirableStorage($app['storage.main.pdo']);
});
