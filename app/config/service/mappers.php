<?php
/**
 * Mappers
 * @var $app Api\Application
 */

use Api\Mapper\DB\BannerMapper;
use Api\Mapper\DB\BookingMapper;
use Api\Mapper\DB\SessionMapper;
use Api\Mapper\DB\TravelMapper;
use Api\Mapper\DB\CommentMapper;
use Api\Mapper\DB\User\RoleMapper;
use Api\Mapper\DB\UserMapper;
use Api\Mapper\DB\CategoryMapper;
use Api\Mapper\DB\ActionMapper;
use Api\Mapper\DB\FlaggedCommentMapper;

$app['mapper.db.user'] = function ($app) {
    $mapper = new UserMapper($app['dbs']['main']);
    $mapper->setSalt($app['config']['security']['password_salt']);
    return $mapper;
};

$app['mapper.db.sessions'] = function ($app) {
    return new SessionMapper($app['dbs']['main']);
};

$app['mapper.db.travel'] = function ($app) {
    $mapper = new TravelMapper($app['dbs']['main']);
    $mapper->setUserMapper($app['mapper.db.user']);
    $mapper->setCategoryMapper($app['mapper.db.category']);
    $mapper->setActionMapper($app['mapper.db.action']);
    return $mapper;
};

$app['mapper.db.category'] = function ($app) {
    $mapper = new CategoryMapper($app['dbs']['main']);
    return $mapper;
};

$app['mapper.db.comment'] = function ($app) {
    $mapper = new CommentMapper($app['dbs']['main']);
    $mapper->setUserMapper($app['mapper.db.user']);
    return $mapper;
};

$app['mapper.db.booking'] = function ($app) {
    return new BookingMapper($app['dbs']['main']);
};

$app['mapper.db.action'] = function ($app) {
    return new ActionMapper($app['dbs']['main']);
};

$app['mapper.db.banner'] = function ($app) {
    return new BannerMapper($app['dbs']['main']);
};

$app['mapper.db.user_role'] = function ($app) {
    return new RoleMapper($app['dbs']['main']);
};
