<?php
namespace Api\Test;

use Api\Application;
use GuzzleHttp\Client;
use HopTrip\ApiClient\ApiClient;

/**
 * Class FunctionalTestCase
 * @package Api\Test
 * @deprecated Use ApplicationTestCase
 */
abstract class FunctionalTestCase extends ApplicationTestCase
{
    use DatabaseTrait;

    /**
     * @var ApiClient
     */
    protected $client;
    
    public function setUp()
    {
        parent::setUp();
        $this->resetDatabase($this->app);
        $this->client = $this->createApiClient();
    }

    public function createApplication()
    {
        return new Application('test');
    }

    /**
     * Creates a user and logs him in
     * @param string $email
     */
    protected function createAndLoginUser($email = 'sasha@pushkin.ru')
    {
        $password = '123';
        $this->client->registerUser([
            'firstName' => 'Alexander',
            'lastName'  => 'Pushkin',
            'picture'   => 'http://pushkin.ru/sasha.jpg',
            'email'     => $email,
            'password'  => $password,
            'creator'   => true,
        ]);
        $token = $this->client->getTokenByEmail($email, $password);
        $this->client->setAuthToken($token);
    }
}
