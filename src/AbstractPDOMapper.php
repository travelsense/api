<?php
namespace Api;

use PDO;

abstract class AbstractPDOMapper
{
    /**
     * @var array
     */
    protected $driverOptions = [];

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * AbstractMapper constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create object by a DB row
     *
     * @param  array $row
     * @return mixed
     */
    abstract protected function create(array $row);

    /**
     * Create an object from a joined table.
     * If the SQL result contains multiple columns with the same name
     *
     * @param array $row Result set fetched using PDO::FETCH_NAMED method
     * @param AbstractPDOMapper[] $mappers Variadic list of mappers
     * @return array Array of created objects
     */
    protected function createFromJoined(array $row, self ...$mappers): array
    {
        $objects = [];
        foreach ($mappers as $index => $mapper) {
            $objects[] = $mapper->create($this->normalize($row, $index));
        }
        return $objects;
    }

    /**
     * Replace keys which are arrays with their $index-th elements
     * @param array $row
     * @param int $index
     * @return array
     */
    private function normalize(array $row, int $index): array
    {
        foreach ($row as $key => $item) {
            if (is_array($item)) {
                $row[$key] = $item[$index];
            }
        }
        return $row;
    }
}
