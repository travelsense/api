<?php
/**
 * @var $app Api\Application
 */
use Api\Wego\WegoFlights;
use Api\Wego\WegoHotels;
use Api\Wego\WegoHttpClient;

$app['wego.http'] = function ($app) {
    return new WegoHttpClient($app['config']['wego']['key'], $app['config']['wego']['ts_code']);
};
$app['wego.hotels'] = function ($app) {
    return new WegoHotels($app['wego.http']);
};
$app['wego.flights'] = function ($app) {
    return new WegoFlights($app['wego.http']);
};
