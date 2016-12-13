<?php
namespace Api\Mapper\DB;

use Doctrine\DBAL\Connection;

trait ConnectionDependentTrait
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
}
