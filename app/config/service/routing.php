<?php
// Routing

$toDate = function (string $date): DateTime {
    return new DateTime($date);
};

$toInt = function (string $val): int {
    return intval($val);
};

$iataType = '^country|city|port|carrier$';

/** @var $app Api\Application */

// User

$app->post('/user', 'controller.user:createUser')
    ->bind('create-user');

$app->get('/user', 'controller.user:getUser');

$app->put('/user', 'controller.user:updateUser');

$app->post('/email/confirm/{token}', 'controller.user:confirmEmail')
    ->bind('confirm-email');

$app->post('/password/reset/{token}', 'controller.user:resetPassword')
    ->bind('reset-password');

$app->post('/password/link/{email}', 'controller.user:sendPasswordResetLink')
    ->bind('send-password-reset-link');

$app->post('/token', 'controller.auth:create')
    ->bind('create-token');

// Uber

$app->get('/uber/price/{lat1}/{lon1}/{lat2}/{lon2}', 'controller.uber:getPriceEstimate');

// Stats

$app->get('/stats', 'controller.booking:getStats');

// Travel

$app->get('/travel/by-user', 'controller.travel:getUserTravels')
    ->bind('travel-by-user');

$app->get('/travel/by-category/{name}', 'controller.travel:getTravelsByCategory')
    ->bind('travel-by-category');

$app->get('/travel/featured', 'controller.travel:getFeatured');

$app->get('/travel/favorite', 'controller.travel:getFavorites');

$app->post('/travel/favorite/{id}', 'controller.travel:addFavorite')
    ->convert('id', $toInt);

$app->delete('/travel/favorite/{id}', 'controller.travel:removeFavorite')
    ->convert('id', $toInt);

$app->post('/travel/comment/{id}/flag', function() { return [];}) // TODO Implement flagging
    ->convert('id', $toInt);

$app->get('/travel/{id}/comments', 'controller.comment:getAllByTravelId')
    ->convert('id', $toInt)
    ->bind('travel-comment');

$app->post('/travel/{id}/comment', 'controller.comment:createTravelComment')
    ->convert('id', $toInt);

$app->post('/travel/{id}/book', 'controller.booking:registerBooking')
    ->convert('id', $toInt);

$app->delete('/travel/comment/{id}', 'controller.comment:deleteById')
    ->convert('id', $toInt);

$app->get('/travel/{id}', 'controller.travel:getTravel')
    ->convert('id', $toInt)
    ->bind('travel-by-id');

$app->put('/travel/{id}', 'controller.travel:updateTravel')
    ->convert('id', $toInt);

$app->delete('/travel/{id}', 'controller.travel:deleteTravel')
    ->convert('id', $toInt);

$app->post('/travel', 'controller.travel:createTravel');

// Travel categories

$app->get('/categories', 'controller.categories:getCategories')
    ->bind('travel-category');

// IATA entities

$app->get('/iata/{type}/code/{code}', 'controller.iata:getOne')
    ->assert('type', $iataType)
    ->bind('iata-by-code');

$app->get('/iata/{type}/all', 'controller.iata:getAll')
    ->assert('type', $iataType)
    ->bind('iata-all');

// Hotel

$app->post('/hotel/search/{location}/{in}/{out}/{rooms}', 'controller.wego:startSearch')
    ->convert('in', $toDate)
    ->convert('out', $toDate)
    ->convert('rooms', $toInt);

$app->get('/hotel/search-results/{id}/{page}', 'controller.wego:getSearchResults')
    ->convert('page', $toInt);

// Health check

$app->get('/healthCheck', 'controller.health:healthCheck')
    ->bind('health-check');

$app->get('/version/{version}', 'controller.client:version')
    ->bind('version');
