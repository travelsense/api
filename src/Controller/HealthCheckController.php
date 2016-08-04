<?php
namespace Api\Controller;

use Api\Migrator\Factory;

class HealthCheckController extends ApiController
{
    /**
     * @var Factory
     */
    private $migrator_factory;

    /**
     * @var string[]
     */
    private $database_names;

    /**
     * HealthCheckController constructor.
     * @param Factory   $migrator_factory
     * @param string[] $database_names
     */
    public function __construct(Factory $migrator_factory, array $database_names)
    {
        $this->migrator_factory = $migrator_factory;
        $this->database_names = $database_names;
    }

    /**
     * @return array
     */
    public function healthCheck(): array
    {
        $db = [];
        foreach ($this->database_names as $name) {
            $versions = [];
            list ($versions['min'], $versions['current'], $versions['max']) = $this->migrator_factory
                ->getMigrator($name)
                ->getVersionRange();
            $db[$name] = $versions;
        }
        return [
            'env' => getenv('APP_ENV'),
            'db' => $db,
        ];
    }
}
