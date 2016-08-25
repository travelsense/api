<?php
namespace Api\Test;

use Api\Application;
use HopTrip\ApiClient\ApiClient;

abstract class FunctionalTestCase extends \PHPUnit_Framework_TestCase
{
    use PHPServerTrait;
    use DatabaseTrait;

    /**
     * @var ApiClient
     */
    protected $client;
    
    public static function setUpBeforeClass()
    {
        self::startServer('/tmp/php-server.log');
    }

    public static function tearDownAfterClass()
    {
        self::stopServer();
    }
    
    public function setUp()
    {
        $app = Application::createByEnvironment();
        $env = $app['env'];
        if ($env !== 'test') {
            $this->markTestSkipped("Functional tests are disabled on this environment: $env");
        }
        $this->resetDatabase($app);
        $this->client = new ApiClient(sprintf('%s:%s', self::$host, self::$port));
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
