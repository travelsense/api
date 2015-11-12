<?php

use Symfony\Component\HttpFoundation\Request;

class CustomCallbackControllerResolver extends \Silex\ControllerResolver
{
    protected function doGetArguments(Request $request, $controller, array $parameters)
    {
        if ($this->app->offsetExists('controller_resolver_callback')) {
            $this->app['controller_resolver_callback']($request, $controller, $parameters);
        }
        return parent::doGetArguments($request, $controller, $parameters);
    }
}