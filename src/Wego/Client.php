<?php
namespace Wego;


use PHPCurl\CurlHttp\HttpClient;

class Client
{
    /**
     * @var HttpClient
     */
    private $http;

    /**
     * Client constructor.
     * @param HttpClient $http
     */
    public function __construct(HttpClient $http)
    {
        $this->http = $http ?: new HttpClient();
        
    }


}
