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
    private $conf;

    public function __construct(Connection $connection, array $conf)
    {
        $this->connection = $connection;
        $this->conf = $conf;
    }

    /**
     * Create statistic about users and travels
     */
    public function buildStats()
    {
        $sql = [];
        foreach ($this->conf as $key => $name) {
            $sql[$key] = " ('$name', (SELECT COUNT(*) FROM $name))";
        }
        $insert = $this->connection->prepare('INSERT INTO stats (name, value) VALUES' . implode(',', $sql));
        $insert->execute();
    }

    /**
     * Getting statistic about users and travels
     *
     * @param DateTime $date
     * @return array
     */
    public function getStats(DateTime $date): array
    {
        $sql = [];
        foreach ($this->conf as $key => $name) {
            $sql[$key] = " (CASE name WHEN '$name' THEN value END) AS $name";
        }
        $select = $this->connection->prepare('SELECT '. implode(',', $sql) . ' FROM stats WHERE date = :date');
        $select->execute(
            [
                ':date' => $date->format('Y-m-d'),
            ]
        );
        return $select->fetchAll(\PDO::FETCH_ASSOC);
    }
}
