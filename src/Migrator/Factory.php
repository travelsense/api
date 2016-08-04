<?php
namespace Api\Migrator;

use Api\Application;
use Migrator\Factory\FactoryInterface;
use Migrator\MigrationReader\SingleFolder;
use Migrator\Migrator;

class Factory implements FactoryInterface
{
    /**
     * @var Application
     */
    private $app;

    /**
     * Factory constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @inheritdoc
     */
    public function getMigrator($database)
    {
        $pdo = $this->app["db.{$database}.pdo"];
        $reader = new SingleFolder($this->app['config']['migrations'] . "/$database");
        $log = $this->app['db.migrator.log'];
        return new Migrator($pdo, $reader, $log);
    }
}
