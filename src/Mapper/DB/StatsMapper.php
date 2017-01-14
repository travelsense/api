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

    /**
     * Create statistic about users and travels
     * @param \DateTime $date
     */
    public function buildStats(\DateTime $date)
    {
        $insert = $this->connection->prepare('
           INSERT INTO stats (date, name, value)
           VALUES
             (:date, :users, (SELECT COUNT(*) FROM users)),
             (:date, :travels, (SELECT COUNT(*) FROM travels))
             ON CONFLICT ON CONSTRAINT stats_date_name DO NOTHING
         ');
        $insert->execute(
            [
                ':users'   => 'users',
                ':travels' => 'travels',
                ':date'    => $date->format('Y-m-d'),
            ]
        );
    }

    /**
     * Getting statistic about users and travels
     *
     * @param \DateTime $date
     * @return array
     */
    public function getStats(\DateTime $date): array
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

    public function isUnique(\DateTime $date)
    {
        $select = $this->connection->prepare('SELECT * ');
        $select->execute([':date' => $date->format('Y-m-d')]);
    }
}
