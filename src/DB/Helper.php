<?php
namespace Api\DB;

use Doctrine\DBAL\Driver\Statement;
use InvalidArgumentException;

class Helper
{

    /**
     * Replace values which are arrays with their $index-th elements
     * @param array $row
     * @param int   $index
     * @return array
     */
    public function normalize(array $row, int $index): array
    {
        foreach ($row as $key => $item) {
            if (is_array($item)) {
                $row[$key] = $item[$index];
            }
        }
        return $row;
    }

    /**
     * @param array  $values
     * @param string $key
     * @param array  $params
     * @return string
     */
    public function generateInExpression(array $values, string $key, array &$params = []): string
    {
        $i = 0;
        $generated = [];
        foreach ($values as $val) {
            $generated[":{$key}_{$i}"] = $val;
            $i++;
        }
        $params = array_replace($params, $generated);
        return '(' . implode(', ', array_keys($generated)) . ')';
    }

    public function bindValues(Statement $statement, array $values)
    {
        foreach ($values as $param => $value) {
            $type = gettype($value);
            switch ($type) {
                case "boolean":
                    $statement->bindValue($param, $value, \PDO::PARAM_BOOL);
                    break;
                case "NULL":
                    $statement->bindValue($param, $value, \PDO::PARAM_NULL);
                    break;
                case "integer":
                    $statement->bindValue($param, $value, \PDO::PARAM_INT);
                    break;
                case "string":
                    $statement->bindValue($param, $value, \PDO::PARAM_STR);
                    break;
                default:
                    throw new InvalidArgumentException(
                        "Cannot bind value of type '{$type}' to placeholder '{$param}'"
                    );
            }
        }
    }
}
