<?php
namespace Api\Mapper\DB;

use DateTime;
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

    public function buildStats()
    {
        $insert = $this->connection->prepare('
          INSERT INTO stats (name, value)
          VALUES
            (:users, (SELECT COUNT(*) FROM users)),
            (:travels, (SELECT COUNT(*) FROM travels))
        ');
        $insert->execute(
            [
                ':users' => 'users',
                ':travels'  => 'travels',
            ]
        );
    }

    public function getStats(DateTime $date): array
    {
        $select = $this->connection->prepare('
            SELECT
              (CASE name WHEN :users THEN value END) AS users,
              (CASE name WHEN :travels THEN value END) AS travels
            FROM stats
            WHERE date >= :date
        ');
        $select->execute(
            [
                ':users' => 'users',
                ':travels' => 'travels',
                ':date' => $date->format('Y-m-d')
            ]
        );
        return $select->fetchAll(\PDO::FETCH_ASSOC);
    }
}
