<?php
/**
 * Application configuration
 *
 * @var $app Application
 */

use Api\ControllerResolver;
use Api\Exception\ApiException;
use Api\Exception\ValidationException;
use Api\JSON\FormatException;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Sorien\Provider\PimpleDumpProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app['debug'] = $app['config']['debug'];
$app['resolver'] = $app->share(function () use ($app) {
    // Use the project specific ControllerResolver
    return new ControllerResolver($app, $app['logger']);
});

$app->error(function(FormatException $e, $code) use ($app) {
    throw new ApiException($e->getMessage(), ApiException::VALIDATION, $e);
});

$app->error(function(ApiException $e, $code) use ($app) {
    $response = [
        'error' => $e->getMessage(),
        'code' => $e->getCode(),
    ];
    return $app->json($response, $e->getHttpCode());
});

$app->error(function(Exception $e, $code) use ($app) {
    if ($app['debug']) {
        error_log($e);
        return null; // let the internal handler show the exception
    }
    $codeToMessage = $app['config']['error_message_mapping'];
    if (array_key_exists($code, $codeToMessage)) {
        $message = $codeToMessage[$code];
    } else {
        error_log($code);
        error_log($e);
        $message = $codeToMessage['default'];
    }
    return $app->json([
        'error' => $message,
        'code' => $code,
    ], $code);
});

// Register additional HTTP GET arguments
$app->before(function (Request $request) {
    if ($request->isMethod(Request::METHOD_GET)) {
        foreach ($request->query as $key => $val) {
            if (false === $request->attributes->has($key)) {
                $request->attributes->set($key, $val);
            }
        }
    }
});

// JSON Response
$app->view(function(array $response) use ($app) {
    return $app->json($response);
});

$app->after(function(Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
});

// Twig
$app->register(new TwigServiceProvider , [
    'twig.path' => __DIR__.'/../../view',
]);

// Monolog
$app->register(new MonologServiceProvider, [
    'monolog.logfile' => $app['config']['log']['main'],
    'monolog.name' => 'vaca',
]);

// Pimple dumper
$app->register(new PimpleDumpProvider());
