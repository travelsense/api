<?php
namespace Test;

use Application;
use PDO;
use PDOException;
use Silex\WebTestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class FunctionalTestCase extends WebTestCase
{
    use DBTesting;

    protected $databases = ['main'];

    protected $client;

    /**
     * @param $database
     * @return PDO
     */
    protected function getPdo($database)
    {
        try {
            return $this->app["storage.$database.pdo"];
        } catch (PDOException $e) {
            $this->markTestSkipped('PDOException: '. $e->getMessage());
        }
    }

    public function setUp()
    {
        parent::setUp();
        foreach ($this->databases as $db) {
            $pdo = $this->getPdo($db);
            $this->tearDownDatabase($pdo, $db); // needed in case the previous run has failed
            $this->setUpDatabase($pdo, $db);
        }
    }

    public function tearDown()
    {
        foreach ($this->databases as $db) {
            $this->tearDownDatabase($this->getPdo($db), $db);
        }
        parent::tearDown();
    }

    /**
     * Creates the application.
     *
     * @return HttpKernelInterface
     */
    public function createApplication()
    {
        $app = Application::createByEnvironment('test');
        unset($app['exception_handler']);
        unset($app['email.mandrill']);
        $app['debug'] = true;
        $app['email.mandrill.messages'] = new MandrillMessagesLogger();
        return $app;
    }

    /**
     * @param array $server
     * @return ApiClient
     */
    public function createApiClient(array $server = [])
    {
        return new ApiClient($this->app, $server);
    }
}