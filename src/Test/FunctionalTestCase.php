<?php
namespace Api\Test;

use Api\Application;

abstract class FunctionalTestCase extends \PHPUnit_Framework_TestCase
{
    use PHPServerTrait;
    use DatabaseTrait;

    /**
     * @var ApiClient
     */
    protected $apiClient;
    
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
        $this->apiClient = new ApiClient(sprintf('%s:%s', self::$host, self::$port));
    }

    /**
     * Creates a user and logs him in
     * @param string $email
     */
    protected function createAndLoginUser($email = 'sasha@pushkin.ru')
    {
        $password = '123';
        $this->apiClient->registerUser([
            'firstName' => 'Alexander',
            'lastName'  => 'Pushkin',
            'picture'   => 'http://pushkin.ru/sasha.jpg',
            'email'     => $email,
            'password'  => $password,
        ]);
        $token = $this->apiClient->getTokenByEmail($email, $password);
        $this->apiClient->setAuthToken($token);
    }
}
