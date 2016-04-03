<?php
namespace Api;

use PDO;
use PDOStatement;

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
     * @param string $sql
     * @return PDOStatement
     */
    public function prepare(string $sql): PDOStatement
    {
        return $this->pdo->prepare($sql, $this->driverOptions);
    }

    /**
     * Fetch an object
     *
     * @param  PDOStatement $stmt
     * @return mixed|null
     */
    public function fetch(PDOStatement $stmt)
    {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->create($row) : null;
    }

    /**
     * Create an object from a joined table
     *
     * @param  array  $row
     * @param  string $alias
     * @return mixed
     */
    public function createFromAlias(array $row, string $alias)
    {
        $alias = $alias . '.';
        $prefixLen = mb_strlen($alias);
        foreach ($row as $key => $item) {
            if (0 === mb_strpos($key, $alias) && mb_strlen($key) > $prefixLen) {
                $row[mb_substr($key, $prefixLen)] = $item;
            }
        }
        return $this->create($row);
    }

    /**
     * Create object by a DB row
     *
     * @param  array $row
     * @return mixed
     */
    abstract protected function create(array $row);
}
