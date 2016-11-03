<?php
namespace Api\Mapper\DB;

use Api\DB\AbstractMapper;
use PDO;

class IATAMapper extends AbstractMapper
{
    private $table = [
        'country' => 'iata_countries',
        'city'    => 'iata_cities',
        'state'   => 'iata_states',
        'port'    => 'iata_ports',
        'carrier' => 'iata_carriers',
    ];

    /**
     * @param string $type
     * @param string $code
     * @return false|array
     */
    public function fetchOne(string $type, string $code)
    {
        $select = $this->connection->prepare("SELECT * FROM {$this->table[$type]} WHERE code = :code");
        $select->execute([
            ':code' => $code,
        ]);
        return $select->fetch(PDO::FETCH_NAMED);
    }

    /**
     * @param string $type
     * @param int    $offset
     * @param int    $limit
     * @return array
     */
    public function fetchAll(string $type, int $limit, int $offset): array
    {
        $select = $this->connection->prepare(
            "SELECT * FROM {$this->table[$type]} ORDER BY code ASC LIMIT :limit OFFSET :offset"
        );
        $select->execute([
            ':limit'  => $limit,
            ':offset' => $offset,
        ]);
        return $select->fetchAll(PDO::FETCH_NAMED);
    }
}
