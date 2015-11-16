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

$app->error(function(Exception $e, $code) use ($app) {
    if ($app['debug']) {
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

$this['resolver'] = $this->share(function () use ($app) {
    return new CustomCallbackControllerResolver($app, $app['logger']);
});

$this['controller_resolver_callback'] = $this->protect(function (Request $request, $controller, array $parameters) use ($app) {
    /** @var ReflectionParameter $param */
    foreach ($parameters as $param) {
        if ($param->getClass() && $param->getClass()->isSubclassOf('Api\\Request\\Request')) {
            /** @var Api\Request\Request $controllerParameter */
            $controllerParameter = $param->getClass()->newInstance();
            $controllerParameter->init(json_decode($request->getContent(), true));
            $errors = $app['validator']->validate($controllerParameter);
            if (count($errors)) {
                throw new ValidationException($errors);
            }
            $request->attributes->set($param->getName(), $controllerParameter);
            break;
        }
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

$app['validator'] = $app->share(function ($app) {
    return $app['validator.builder']->getValidator();
});

$app['validator.builder'] = $app->share(function ($app) {
    $builder = Validation::createValidatorBuilder();
    $builder->enableAnnotationMapping(new CachedReader(new AnnotationReader(), new ArrayCache()));
    return $builder;
});

// Workaround to avoid "Exception: Serialization of 'Closure' is not allowed"
// The error happens with PHPUnit when there is a closure in the global namespace
// @see http://stackoverflow.com/questions/4366592/symfony-2-doctrine-2-phpunit-3-5-serialization-of-closure-exception
$app['autoloader'] = $app->share(function() {
    $autoloaders = spl_autoload_functions();
    return $autoloaders[0];
});
AnnotationRegistry::registerLoader($app['autoloader']);
