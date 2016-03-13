<?php
namespace Api\Migrator;


use Api\Application;
use Api\Migrator\Command\Status;
use Api\Migrator\Command\Update;

class ConsoleApp extends \Symfony\Component\Console\Application
{
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        parent::__construct('migrator', '0.0.0');
        $this->add(new Status());
        $this->add(new Update());
        $this->app = $app;
    }

    /**
     * @param string $name
     * @return Migrator
     */
    public function getMigrator($name)
    {
        /** @var Migrator $migrator */
        $migrator =  $this->app["db.$name.migrator"];
        return $migrator;
    }
}
