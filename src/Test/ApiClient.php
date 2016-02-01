<?php
namespace Test;

use GuzzleHttp\Client as HttpClient;

class ApiClient
{
    /**
     * @var HttpClient
     */
    private $http;

    /**
     * @var string
     */
    private $authToken;

    /**
     * ApiClient constructor.
     * @param string $apiUrl
     */
    public function __construct($apiUrl)
    {
        $this->http = new HttpClient([
            'base_uri' => $apiUrl,
            'timeout' => 1.0,
        ]);
    }

    /**
     * @param string $authToken
     */
    public function setAuthToken($authToken)
    {
        $this->authToken = $authToken;
    }

    /**
     * Register new user
     * @param array $user
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function registerUser(array $user)
    {
        return $this->http->post('/user', ['json' => $user]);
    }
}