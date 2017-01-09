<?php
namespace Api\Mapper\DB;

use Doctrine\DBAL\Driver\Connection;

class StatsMapper
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function addUser()
    {
        $sql = 'INSERT INTO stats (name, value) VALUES (:name, :value) RETURNING id';
        $insert = $this->connection->prepare($sql);
        $insert->execute(
            [
                ':name' => 'users',
                ':value'  => 1,
            ]
        );
    }

    public function addTravel()
    {
        $sql = 'INSERT INTO stats (name, value) VALUES (:name, :value) RETURNING id';
        $insert = $this->connection->prepare($sql);
        $insert->execute(
            [
                ':name' => 'travels',
                ':value'  => 1,
            ]
        );
    }
}
