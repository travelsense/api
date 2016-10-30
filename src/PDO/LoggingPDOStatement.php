<?php
namespace Api\PDO;

use LazyPDO\SimplePDOStatementDecorator;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LogLevel;

class LoggingPDOStatement extends SimplePDOStatementDecorator
{
    use LoggerAwareTrait;

    /**
     * Slow query threshold in microseconds
     * @var int
     */
    private $slow_query_threshold = 1000;

    /**
     * @inheritdoc
     */
    public function execute($parameters = null)
    {
        $start = microtime(true);
        $result = parent::execute($parameters);
        if ($this->logger) {
            $time = microtime(true) - $start;
            $this->logger->log(
                ($time > $this->slow_query_threshold) ? LogLevel::WARNING : LogLevel::DEBUG,
                $this->getQueryString(),
                [
                    'parameters' => $parameters,
                    'result' => $result,
                    'time' => $time,
                ]
            );
        }
        return $result;
    }

    /**
     * @param int $slow_query_threshold Microseconds
     */
    public function setSlowQueryThreshold(int $slow_query_threshold)
    {
        $this->slow_query_threshold = $slow_query_threshold;
    }
}
