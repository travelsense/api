<?php
/**
 * @var $app Api\Application
 */

use Api\Application;
use Api\ExpirableStorage;
use Api\Service\ImageCopier;
use Api\Service\ImageStorage;
use Api\Service\PdfGenerator;
use Api\Service\StatService;
use Api\Service\UserPicUpdater;
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
        new \Api\Service\SilentMPDF(),
        $conf['permissions'],
        $conf['password'],
        $conf['key_length']
    );
};

$app['image_storage'] = function (Application $app) {
    $conf = $app['config']['image_upload'];
    $service = new ImageStorage($conf['allowed_mime_types'], $conf['dir'], $conf['base_url']);
    $service->setSizeLimit($conf['size_limit']);
    return $service;
};

$app['image_copier'] = function (Application $app) {
    return new ImageCopier(
        $app['image_storage'],
        $app['config']['image_copier']['timeout']
    );
};

$app['stats_service'] = function (Application $app) {
    return new StatService($app['mapper.db.stats'], $app['email.service']);
};

$app['swift_email_generator'] = function (Application $app) {
    return new \Api\Service\SwiftEmailGenerator(
        $app['config']['email']['from_address'],
        $app['config']['email']['from_name'],
        $app['config']['email']['error_receivers']
    );
};
