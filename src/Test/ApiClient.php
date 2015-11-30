<?php
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

    /**
     * @return mixed
     */
    public function getJson()
    {
        return json_decode($this->getResponse()->getContent(), true);
    }

    /**
     * Register by email and password
     * @param $email
     * @param $password
     * @param $firstName
     * @param $lastName
     */
    public function callRegister($email, $password, $firstName, $lastName)
    {
        $json = json_encode([
            'email' => $email,
            'password' => $password,
            'firstName' => $firstName,
            'lastName' => $lastName,
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

    /**
     * Login by email and password
     * @param $email
     * @param $password
     */
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

    /**
     * Login facebook token
     * @param $token
     */
    public function callLoginFacebook($token)
    {
        $json = json_encode([
            'token' => $token,
        ]);

        $this->request(
            'POST',
            'https://example.com/user/login-by-facebook',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            $json
        );
    }



    /**
     * Get user object
     */
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

}