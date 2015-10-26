<?php
/**
 * Storage
 *
 * @var $this Application
 */

$this['storage.pdo.main'] = $this->share(function ($app) {
    $main = $app['config']['storage']['main'];
    return new PDO(
        sprintf('%s:host=%s;dbname=%s', $main['driver'], $main['host'], $main['database']),
        $main['user'],
        $main['password'],
        $main['options']
    );
});
