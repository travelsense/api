<?php
namespace Api;

use Api\PDO\Helper;
use PDO;
use PDOStatement;

abstract class AbstractPDOMapper
{
    /**
     * @var array
     */
    protected $driver_options = [];

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * AbstractMapper constructor.
     *
     * @param PDO    $pdo
     * @param Helper $helper
     */
    public function __construct(PDO $pdo, Helper $helper = null)
    {
        $this->pdo = $pdo;
        $this->helper = $helper ?: new Helper();
    }

    /**
     * Create object by a DB row
     *
     * @param  array $row
     * @return mixed
     */
    abstract protected function create(array $row);

    /**
     * Create object with all dependencies.
     * This method is to be overloaded in child classes
     * @param array $row
     * @return mixed
     */
    protected function build(array $row)
    {
        return $this->create($row);
    }

    /**
     * @param PDOStatement $statement
     * @return array
     */
    protected function buildAll(PDOStatement $statement): array
    {
        $list = [];
        while ($row = $statement->fetch(PDO::FETCH_NAMED)) {
            $list[] = $this->build($row);
        }
        return $list;
    }

    /**
     * @param PDOStatement $statement
     * @return array
     */
    protected function createAll(PDOStatement $statement): array
    {
        $list = [];
        while ($row = $statement->fetch(PDO::FETCH_NAMED)) {
            $list[] = $this->create($row);
        }
        return $list;
    }

    /**
     * Create an object from a joined table.
     * If the SQL result contains multiple columns with the same name
     *
     * @param array               $row     Result set fetched using PDO::FETCH_NAMED method
     * @param AbstractPDOMapper[] $mappers Variadic list of mappers
     * @return array Array of created objects
     */
    protected function createFromJoined(array $row, self ...$mappers): array
    {
        $objects = [];
        foreach ($mappers as $index => $mapper) {
            $objects[] = $mapper->create($this->helper->normalize($row, $index));
        }
        return $objects;
    }
}
