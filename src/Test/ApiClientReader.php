<?php
namespace Api\Test;


use HopTrip\ApiClient\ApiClient;
use HopTrip\ApiClient\ApiClientException;
use RuntimeException;

class ApiClientReader
{
    use PHPServerTrait;

    /**
     * @var ApiClient
     */
    private $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function __call($method, array $params)
    {
        try {
            return call_user_func_array(array($this->client, $method), $params);
        } catch (RuntimeException $ex) {
            $message = $ex->getMessage();
            if ($message == 'Internal Server Error') {
                $message = $message . self::getFileContent(self::getLogPath());
                throw new ApiClientException($message, 500);
            }
            return call_user_func_array(array($this->client, $method), $params);
        }
    }
}