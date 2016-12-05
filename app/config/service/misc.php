<?php
/**
 * @var $app Api\Application
 */

use Api\Application;
use Api\ExpirableStorage;
use Api\Service\ImageLoader;
use Api\Service\PdfGenerator;
use F3\SimpleUber\Uber;
use Facebook\Facebook;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

$app['facebook'] = function ($app) {
    return new Facebook($app['config']['facebook']);
};

$app['password_generator'] = function ($app) {
    $generator = new ComputerPasswordGenerator();
    $generator
        ->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, true)
        ->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true)
        ->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true)
        ->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, false);

    return $generator;
};

$app['uber'] = function ($app) {
    return new Uber($app['config']['uber']['server_token']);
};

$app['storage.expirable_storage'] = function (Application $app) {
    return new ExpirableStorage($app['dbs']['main']);
};

$app['pdf_generator'] = function (Application $app) {
    $conf = $app['config']['pdf_generator'];
    return new PdfGenerator(
        $conf['permissions'],
        $conf['password'],
        $conf['key_length']
    );
};

$app['image_loader'] = function (Application $app) {
    $conf = $app['config']['image_upload'];
    $service = new ImageLoader($conf['allowed_mime_types'], $conf['dir'], $conf['base_url']);
    $service->setLogger($app['logger']);
    return $service;
};
