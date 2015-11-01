<?php
/**
 * Controllers
 * @var $app Application
 */

$app['controller.activity'] = $app->share(function($app) {
    return new Controller\Activity($app['storage.main.pdo']);
});
$app['controller.test'] = $app->share(function($app) {
    return new Controller\Test();
});