<?php
/**
 * Application configuration
 *
 * @var $app Application
 */

// Api Exceptions to be shown to user
use Symfony\Component\HttpFoundation\Request;

$app['debug'] = $app['config']['debug'];

$app->error(function(Exception\ApiException $e) use ($app) {
    return $app->json([
        'error' => $e->getMessage(),
        'code' => $e->getCode(),
    ], $e->getHttpCode());
});


// General Exceptions to be logged or shown
$app->error(function(Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
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

// JSON Request. JSON payload to be set as json attribute
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->attributes->set('payload', $data);
    }
});

// Register additional HTTP GET arguments
$app->before(function (Request $request) {
    if ($request->isMethod(Request::METHOD_GET)) {
        foreach ($request->query as $key => $val) {
            if ( ! $request->attributes->has($key)) {
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
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../../view',
));

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => '/tmp/wtf.log',
));
