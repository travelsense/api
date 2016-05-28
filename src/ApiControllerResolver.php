<?php
namespace Api;

use ReflectionParameter;
use Silex\ControllerResolver;
use Symfony\Component\HttpFoundation\Request;

class ApiControllerResolver extends ControllerResolver
{
    protected function doGetArguments(Request $request, $controller, array $parameters)
    {
        /* @var ReflectionParameter $param */
        foreach ($parameters as $param) {
            if ($param->getClass() && $param->getClass()->getName() === 'Api\\Model\\User') {
                $request->attributes->set($param->getName(), $this->app['user']);
                break;
            }
        }
        return parent::doGetArguments($request, $controller, $parameters);
    }
}
