<?php
/**
 * Application configuration
 *
 * @var $app Application
 */

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Exception\ApiException;
use Exception\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;

$app['debug'] = $app['config']['debug'];
$app['resolver'] = $app->share(function () use ($app) {
    // Use the project specific ControllerResolver
    return new ControllerResolver($app, $app['logger']);
});

$app->error(function(Exception $e, $code) use ($app) {
    if ($app['debug']) {
        error_log($e);
        return null; // let the internal handler show the exception
    }
    if ($e instanceof ApiException) {
        $response = [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
        ];
        if ($e instanceof ValidationException) {
            foreach ($e->getViolations() as $violation) {
                $response['violations'][] = [
                    'message' => $violation->getMessage(),
                    'code' => $violation->getCode(),
                    'property' => $violation->getPropertyPath(),
                ];
            }
        }
        return $app->json($response, $e->getHttpCode());
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

// Twig
$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../../view',
]);

// Monolog
$app->register(new Silex\Provider\MonologServiceProvider(), [
    'monolog.logfile' => $app['config']['log']['main'],
    'monolog.name' => 'vaca',
]);

// Pimple dumper
$app->register(new Sorien\Provider\PimpleDumpProvider());
