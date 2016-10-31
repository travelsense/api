<?php
/**
 * Mappers
 * @var $app Api\Application
 */

use Api\ExpirableStorage;
use Api\Mapper\DB\BannerMapper;
use Api\Mapper\DB\BookingMapper;
use Api\Mapper\DB\IATAMapper;
use Api\Mapper\DB\SessionMapper;
use Api\Mapper\DB\TravelMapper;
use Api\Mapper\DB\CommentMapper;
use Api\Mapper\DB\User\RoleMapper;
use Api\Mapper\DB\UserMapper;
use Api\Mapper\DB\CategoryMapper;
use Api\Mapper\DB\ActionMapper;
use Api\Mapper\DB\FlaggedCommentMapper;

$app['mapper.db.user'] = function ($app) {
    $mapper = new UserMapper($app['db.main.connection']);
    $mapper->setSalt($app['config']['security']['password_salt']);
    return $mapper;
};

$app['mapper.db.sessions'] = function ($app) {
    return new SessionMapper($app['db.main.connection']);
};

$app['mapper.db.iata'] = function ($app) {
    return new IATAMapper($app['db.main.connection']);
};

$app['mapper.db.travel'] = function ($app) {
    $mapper = new TravelMapper($app['db.main.connection']);
    $mapper->setUserMapper($app['mapper.db.user']);
    $mapper->setCategoryMapper($app['mapper.db.category']);
    $mapper->setActionMapper($app['mapper.db.action']);
    return $mapper;
};

$app['mapper.db.category'] = function ($app) {
    $mapper = new CategoryMapper($app['db.main.connection']);
    return $mapper;
};

$app['mapper.db.comment'] = function ($app) {
    $mapper = new CommentMapper($app['db.main.connection']);
    $mapper->setUserMapper($app['mapper.db.user']);
    return $mapper;
};

$app['mapper.db.flagged_comment'] = function ($app) {
    return new FlaggedCommentMapper($app['db.main.connection']);
};

$app['mapper.db.booking'] = function ($app) {
    return new BookingMapper($app['db.main.connection']);
};

$app['mapper.db.action'] = function ($app) {
    return new ActionMapper($app['db.main.connection']);
};

$app['mapper.db.banner'] = function ($app) {
    return new BannerMapper($app['db.main.connection']);
};

$app['mapper.db.user_role'] = function ($app) {
    return new RoleMapper($app['db.main.connection']);
};
