<?php
/**
 * Created by PhpStorm.
 * User: f3ath
 * Date: 11/8/15
 * Time: 8:42 PM
 */

namespace Test;



use Symfony\Component\HttpKernel\Client;

class ApiClient extends Client
{
    /**
     * @var string
     */
    private $authToken;

    /**
     * @param string $authToken
     */
    public function setAuthToken($authToken)
    {
        $this->authToken = $authToken;
    }

    public function callRegister($email, $password)
    {
        $json = json_encode([
            'email' => $email,
            'password' => $password,
        ]);

        $this->request(
            'POST',
            'https://example.com/user/register-by-email',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            $json
        );
    }

    public function callLoginByEmail($email, $password)
    {
        $json = json_encode([
            'email' => $email,
            'password' => $password,
        ]);

        $this->request(
            'POST',
            'https://example.com/user/login-by-email',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            $json
        );
    }

    public function callUser()
    {
        $this->request(
            'GET',
            'https://example.com/user',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => 'Token ' . $this->authToken,
            ]
        );
    }

    /**
     * @return mixed
     */
    public function getJson()
    {
        return json_decode($this->getResponse()->getContent(), true);
    }
}