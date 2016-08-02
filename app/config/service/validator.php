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

$app['json_schema_validator'] = function ($app) {
    return new JsonSchemaValidator();
};

$app['ref_uri_resolver'] = function ($app) {
    return new UriResolver();
};

$app['ref_uri_retriever'] = function ($app) {
    return new UriRetriever();
};

$app['ref_ref_resolver'] = function ($app) {
    return new RefResolver($app['ref_uri_retriever'], $app['ref_uri_resolver']);
};

$app['validator'] = function ($app) {
    return new Validator($app['json_schema_validator'], $app['config']['schema_path'], $app['ref_ref_resolver']);
};
