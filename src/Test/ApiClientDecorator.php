<?php

namespace Api\Test;

class ApiClientDecorator
{

    use PHPServerTrait;

    /**
     * @var Api\Test\ApiClient
     */
    private $object;

    public function __construct(ApiClient $object)
    {
        $this->object = $object;
    }

    public function __call($method, array $params)
    {
        try {
            return call_user_func_array(array($this->object, $method), $params);
        } catch (ApiClientException $ex) {
            $message = $ex->getMessage();
            if ($message == 'Internal Server Error') {
                $message = $message . $this->tailServerLog();
                throw new ApiClientException($message, 500);
            }
            return call_user_func_array(array($this->object, $method), $params);
        }
    }
}
