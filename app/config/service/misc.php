<?php
/**
 * @var $app Application
 */
use Facebook\Facebook;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;

$app['facebook'] = $app->share(function($app) {
   return new Facebook($app['config']['facebook']);
});

$app['password_generator'] = $app->share(function ($app) {
   $generator = new ComputerPasswordGenerator();
   $generator
       ->setOptionValue(ComputerPasswordGenerator::OPTION_UPPER_CASE, true)
       ->setOptionValue(ComputerPasswordGenerator::OPTION_LOWER_CASE, true)
       ->setOptionValue(ComputerPasswordGenerator::OPTION_NUMBERS, true)
       ->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, false);

   return $generator;
});