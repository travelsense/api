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
     * @param float $timeout
     */
    public function __construct($apiUrl, $timeout = 5.0)
    {
        $this->http = new HttpClient([
            'base_uri' => $apiUrl,
            'timeout' => $timeout,
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
     * @param array $user (firstName, lastName, email, password, picture)
     * @return void
     *
     * Example: $client->registerUser([
     *  'firstName' => 'Alexander'
     *  'lastName'=> 'Pushkin',
     *  'email' => 'sashs@nashe-vse.ru',
     *  'password' => 'd4n73s l0h',
     * ]);
     */
    public function registerUser(array $user)
    {
        $this->http->post('/user', ['json' => $user]);
    }

    /**
     * @param string $email
     * @param string $password
     * @return string Auth token
     */
    public function authByEmail($email, $password)
    {
        $json = $this->http
            ->post('/token/by-email/'.urldecode($email), ['json' => $password])
            ->getBody()
            ->getContents();
        return json_decode($json);
    }

    /**
     * Get current user info
     * @return object
     */
    public function getCurrentUser()
    {
        $json = $this->http
            ->get('/user', ['headers' => ['Authorization' => 'Token '.$this->authToken]])
            ->getBody()
            ->getContents();
        return json_decode($json, true);
    }
}