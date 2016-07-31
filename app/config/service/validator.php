<?php
/**
 * Validator service config
 * @var $app Application
 */

use JsonSchema\Validator;

$app['validator'] = function ($app) {
    return new Validator();
};