<?php
/**
 * Mappers
 * @var $app Api\Application
 */

use Api\ExpirableStorage;
use Api\Mapper\DB\BookingMapper;
use Api\Mapper\DB\IATAMapper;
use Api\Mapper\DB\SessionMapper;
use Api\Mapper\DB\TravelMapper;
use Api\Mapper\DB\CommentMapper;
use Api\Mapper\DB\UserMapper;
use Api\Mapper\DB\CategoryMapper;
use Api\Mapper\DB\FlaggedCommentMapper;

$app['mapper.db.user'] = function($app) {
    $mapper =  new UserMapper($app['db.main.pdo']);
    $mapper->setSalt($app['config']['security']['password_salt']);
    return $mapper;
};

$app['mapper.db.sessions'] = function($app) {
    return new SessionMapper($app['db.main.pdo']);
};

$app['mapper.db.iata'] = function($app) {
    return new IATAMapper($app['db.main.pdo']);
};

$app['mapper.db.expirable_storage'] = function($app) {
    return new ExpirableStorage($app['db.main.pdo']);
};

$app['mapper.db.travel'] = function($app) {
    $mapper = new TravelMapper($app['db.main.pdo']);
    $mapper->setUserMapper($app['mapper.db.user']);
    $mapper->setCategoryMapper($app['mapper.db.category']);
    return $mapper;
};

$app['mapper.db.category'] = function($app) {
    $mapper = new CategoryMapper($app['db.main.pdo']);
    return $mapper;
};

$app['mapper.db.comment'] = function($app) {
    $mapper = new CommentMapper($app['db.main.pdo']);
    $mapper->setUserMapper($app['mapper.db.user']);
    return $mapper;
};

$app['mapper.db.flagged_comment'] = function($app) {
    return new FlaggedCommentMapper($app['db.main.pdo']);
};

$app['mapper.db.booking'] = function($app) {
    return new BookingMapper($app['db.main.pdo']);
};