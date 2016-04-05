<?php
namespace Api\Test;

use Api\Application;
use Api\Model\User;
use PDO;

abstract class FunctionalTestCase extends \PHPUnit_Framework_TestCase
{
    use PHPServerTrait;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ApiClient
     */
    protected $apiClient;

    public function setUp()
    {
        $this->app = Application::createByEnvironment();
        $env = $this->app['env'];
        if ($env !== 'test') {
            $this->markTestSkipped("Functional tests are disabled on this environment: $env");
        }
        foreach ($this->app['config']['db'] as $name => $db) {
            $pdo = new PDO(
                "pgsql:dbname=postgres;host={$db['host']}",
                $db['user'],
                $db['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            $pdo->exec("SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = '{$db['database']}'");
            $pdo->exec("DROP DATABASE IF EXISTS {$db['database']}");
            $pdo->exec("CREATE DATABASE {$db['database']} OWNER={$db['user']}");
            $this->app["db.$name.pdo"]->exec(file_get_contents(__DIR__ . '/../../db/ext/postgis.sql'));
            $this->app["db.$name.migrator"]->upgrade();
        }
        $this->startServer();
        $this->apiClient = new ApiClient("$this->host:$this->port");
    }

    public function tearDown()
    {
        $this->stopServer();
        unset($this->app);
    }

    /**
     * Creates a user and logs him in
     */
    protected function createAndLoginUser()
    {
        $email = 'sasha@pushkin.ru';
        $password = '123';
        $this->apiClient->registerUser([
            'firstName' => 'Alexander',
            'lastName' => 'Pushkin',
            'picture' => 'http://pushkin.ru/sasha.jpg',
            'email' => $email,
            'password' => $password,
        ]);
        $token = $this->apiClient->getTokenByEmail($email, $password);
        $this->apiClient->setAuthToken($token);
    }
}
