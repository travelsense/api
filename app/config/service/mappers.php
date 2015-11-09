<?php
/**
 * Mappers
 * @var $app Application
 */

$app['mapper.user'] = $app->share(function($app) {
   return new \Mapper\UserMapper($app['storage.main.pdo']);
});

$app['mapper.sessions'] = $app->share(function($app) {
   return new \Mapper\SessionMapper($app['storage.main.pdo']);
});