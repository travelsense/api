<?php
/**
 * Translator service config
 * @var $app Application
 */

use Silex\Provider\TranslationServiceProvider;

$app->register(new TranslationServiceProvider());
