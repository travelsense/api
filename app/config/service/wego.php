<?php
/** 
 * @var $app Api\Application  
 */
use Api\Wego\WegoHttpClient;
use Api\Wego\WegoHotels;
use Api\Wego\WegoFlights;

$app['wego.http'] = $app->share(function ($app) {
    return new WegoHttpClient($app['config']['wego']['key'], $app['config']['wego']['ts_code']);
});
$app['wego.hotels'] = $app->share(function ($app) {
    return new WegoHotels($app['wego.http']);
});
$app['wego.flights'] = $app->share(function ($app) {
    return new WegoFlights($app['wego.http']);
});
