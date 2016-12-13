<?php
namespace Api\Mapper\DB;

trait ConnectionAwareTrait
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
