<?php
namespace Api\Test;

use RuntimeException;
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
     * @param mixed $payload to be json encoded
     * @return mixed json decoded response
     */
    public function post(string $uri, $payload)
    {
        return $this->rawPost($uri, json_encode($payload));
    }

    /**
     * Do a raw POST request, no json encoding
     * @param string $uri
     * @param string $payload
     * @return mixed
     */
    public function rawPost(string $uri, string $payload)
    {
        $server = [];
        if ($this->token) {
            $server['HTTP_AUTHORIZATION'] = "Token {$this->token}";
        }
        $this->http->request('POST', $uri, [], [], $server, $payload);
        $response = $this->http->getResponse();
        if ($response->getStatusCode() != 200) {
            $payload = json_decode($response->getContent(), true);
            if (isset($payload['code']) && isset($payload['error'])) {
                throw new ApiClientException($payload['error'], $payload['code']);
            }
            throw new RuntimeException($response->getContent(), $response->getStatusCode());
        }
        return json_decode($response->getContent(), true);
    }
}
