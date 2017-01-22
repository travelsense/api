<?php
/**
 * Translator service config
 * @var $app \Api\Application
 */

use Silex\Provider\TranslationServiceProvider;

$app->register(new TranslationServiceProvider());
