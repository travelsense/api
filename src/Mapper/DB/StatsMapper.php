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

    /**
     * Create statistic about users and travels
     */
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

    /**
     * Getting statistic about users and travels
     *
     * @param DateTime $date
     * @return array
     */
    public function getStats(DateTime $date): array
    {
        $select = $this->connection->prepare('
            SELECT
              (CASE name WHEN :users THEN value END) AS users,
              (CASE name WHEN :travels THEN value END) AS travels
            FROM stats
            WHERE date BETWEEN :date AND :ydate
        ');
        $select->execute(
            [
                ':users' => 'users',
                ':travels' => 'travels',
                ':date' => $date->format('Y-m-d'),
                ':ydate' => $date->modify('+1 day')->format('Y-m-d')
            ]
        );
        return $select->fetchAll(\PDO::FETCH_ASSOC);
    }
}
