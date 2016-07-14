<?php
namespace Api;

use PDO;
use PDOStatement;
use InvalidArgumentException;

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
            $objects[] = $mapper->create($this->normalize($row, $index));
        }
        return $objects;
    }

    /**
     * Replace keys which are arrays with their $index-th elements
     * @param array $row
     * @param int   $index
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

    public function bindValues(PDOStatement $statement, array $values)
    {
        foreach ($values as $param => $value) {
            $type = gettype($value);
            switch ($type) {
                case "boolean":
                    $statement->bindValue($param, $value, PDO::PARAM_BOOL);
                    break;
                case "NULL":
                    $statement->bindValue($param, $value, PDO::PARAM_NULL);
                    break;
                case "integer":
                    $statement->bindValue($param, $value, PDO::PARAM_INT);
                    break;
                case "string":
                    $statement->bindValue($param, $value, PDO::PARAM_STR);
                    break;
                default:
                    throw new InvalidArgumentException(
                        "Cannot bind value of type '{$type}' to placeholder '{$param}'"
                    );
            }
        }
    }
}
