<?php
/**
 * Validator service config
 * @var $app Application
 */

use Api\JSON\Validator;
use JsonSchema\RefResolver;
use JsonSchema\Validator as JsonSchemaValidator;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Uri\UriRetriever;

$app['validator.json_schema_validator'] = function ($app) {
    return new JsonSchemaValidator();
};

$app['validator.ref_uri_resolver'] = function ($app) {
    return new UriResolver();
};

$app['validator.ref_uri_retriever'] = function ($app) {
    return new UriRetriever();
};

$app['validator.ref_ref_resolver'] = function ($app) {
    return new RefResolver($app['validator.ref_uri_retriever'], $app['validator.ref_uri_resolver']);
};

$app['validator.validator'] = function ($app) {
    return new Validator($app['validator.json_schema_validator'], $app['config']['schema_path'], $app['validator.ref_ref_resolver']);
};
