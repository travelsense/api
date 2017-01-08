<?php
namespace Api\DB;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;

abstract class AbstractMapper
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * AbstractMapper constructor.
     *
     * @param Connection $connection
     * @param Helper     $helper
     */
    public function __construct(Connection $connection, Helper $helper = null)
    {
        $this->connection = $connection;
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
     * @param Statement $statement
     * @return array
     */
    protected function buildAll(Statement $statement): array
    {
        $list = [];
        while ($row = $statement->fetch(\PDO::FETCH_NAMED)) {
            $list[] = $this->build($row);
        }
        return $list;
    }

    /**
     * @param Statement $statement
     * @return array
     */
    protected function createAll(Statement $statement): array
    {
        $list = [];
        while ($row = $statement->fetch(\PDO::FETCH_NAMED)) {
            $list[] = $this->create($row);
        }
        return $list;
    }

    /**
     * Create an object from a joined table.
     * If the SQL result contains multiple columns with the same name
     *
     * @param array               $row     Result set fetched using PDO::FETCH_NAMED method
     * @param AbstractMapper[] $mappers Variadic list of mappers
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
