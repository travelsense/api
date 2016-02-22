<?php
use Symfony\Component\HttpFoundation\Request;

class ControllerResolver extends Silex\ControllerResolver
{
    protected function doGetArguments(Request $request, $controller, array $parameters)
    {
        /**
 * @var ReflectionParameter $param 
*/
        foreach ($parameters as $param) {
            if ($param->getClass() && $param->getClass()->getName() === 'Model\\User') {
                $request->attributes->set($param->getName(), $this->app['user']);
                break;
            }
        }
        return parent::doGetArguments($request, $controller, $parameters);
    }

}