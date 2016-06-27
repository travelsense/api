<?php
namespace Api;

use Api\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ArgumentValueResolver implements ArgumentValueResolverInterface
{
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return $argument->getType() === User::class;
    }
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->app['user'];
    }
}
