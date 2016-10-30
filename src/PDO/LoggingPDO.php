<?php
namespace Api\PDO;

use LazyPDO\LazyPDO;
use Psr\Log\LoggerAwareTrait;

class LoggingPDO extends LazyPDO
{
    use LoggerAwareTrait;

    /**
     * @inheritdoc
     */
    public function prepare($query, $options = [])
    {
        $statement = new LoggingPDOStatement(
            parent::prepare($query, $options)
        );
        if ($this->logger) {
            $statement->setLogger($this->logger);
        }
        return $statement;
    }
}
