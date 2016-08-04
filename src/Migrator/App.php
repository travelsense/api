<?php
namespace Api\Migrator;

use Api\Application as ApiApplication;
use Migrator\Console\Application;

class App extends Application
{
    public function __construct()
    {
        parent::__construct(
            new Factory(
                ApiApplication::createByEnvironment()
            )
        );
        foreach (['status', 'migrate'] as $command) {
            $this->get($command)
                ->getDefinition()
                ->getArgument('database')
                ->setDefault('main');
        }
    }
}
