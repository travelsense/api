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
        $select = $this->connection->prepare('SELECT name, value FROM stats WHERE date = :date');
        $select->execute([':date' => $date->format('Y-m-d')]);
        $stats = [];
        $response = $select->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($response as $item) {
            $stats[$item['name']] = $item['value'];
        }
        return $stats;
    }
}
