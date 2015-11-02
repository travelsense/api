<?php
/**
 * Mappers
 * @var $app Application
 */

$app['mapper.user'] = $app->share(function($app) {
   return new \Mapper\UserMapper($app['storage.main.pdo']);
});