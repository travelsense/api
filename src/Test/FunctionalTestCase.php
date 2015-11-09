<?php
/**
 * User: f3ath
 * Date: 11/1/15
 * Time: 4:36 PM
 */

namespace Test;


use Application;
use Silex\WebTestCase;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class FunctionalTestCase extends WebTestCase
{
    use DBTesting;

    protected $databases = ['main'];

    protected $client;

    protected function getPdo($database)
    {
        return $this->app["storage.$database.pdo"];
    }

    public function setUp()
    {
        parent::setUp();
        foreach ($this->databases as $db) {
            $this->setUpDatabase($this->getPdo($db), $db);
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