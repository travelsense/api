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

    public function getStats()
    {
        $select = $this->connection->prepare('
            SELECT
              SUM(CASE name WHEN :users THEN value END) AS sum_new_users,
              SUM(CASE name WHEN :travels THEN value END) AS sum_new_travels
            FROM
              stats
        ');
        $select->execute(
            [
                ':users' => 'users',
                ':travels' => 'travels'
            ]
        );
        return $select->fetch(\PDO::FETCH_ASSOC);
    }
}
