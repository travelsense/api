<?php
namespace Api\Test;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;

class ApiClient
{
    /**
     * @var Client
     */
    private $http;

    /**
     * @var string
     */
    private $token;

    /**
     * ApiClient constructor.
     * @param Client $http
     */
    public function __construct(Client $http)
    {
        $this->http = $http;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->token = $token;
    }

    public function getRequest(): Request
    {
        return $this->http->getRequest();
    }

    public function getResponse(): Response
    {
        return $this->http->getResponse();
    }

    /**
     * Do POST request
     * @param string $uri
     * @param        $payload
     * @return mixed json decoded response
     */
    public function post(string $uri, $payload)
    {
        $server = [];
        if ($this->token) {
            $server['HTTP_AUTHORIZATION'] = "Token {$this->token}";
        }
        $this->http->request('POST', $uri, [], [], $server, json_encode($payload));
        $response = $this->http->getResponse();
        if ($response->getStatusCode() != 200) {
            throw new \RuntimeException($response->getContent(), $response->getStatusCode());
        }
        return json_decode($response->getContent(), true);
    }

    public function login(string $email, string $password)
    {
        $this->token = $this->post('/token', ['email' => $email, 'password' => $password])['token'];
    }


}
