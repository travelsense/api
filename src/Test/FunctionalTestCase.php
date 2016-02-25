<?php
namespace Api\Test;

use Api\Application;
use PDOException;
use Weew\HttpServer\HttpServer;

class FunctionalTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $dir;

    /**
     * @var HttpServer
     */
    private $server;

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
        parent::setUp();

        $this->app = Application::createByEnvironment('test');
        try {
            $this->app['storage.main.pdo']
                ->exec(file_get_contents(__DIR__ . '/../../schema/main.install.sql'));
        } catch (PDOException $e) {
            $this->markTestSkipped($e->getMessage());

        }

        $this->dir = getcwd();
        chdir(__DIR__ . '/../../public');

        $this->server = new HttpServer('localhost', 8080, 'app_test.php');
        $this->server->disableOutput();
        $this->server->start();

        $this->apiClient = new ApiClient('localhost:8080');
    }

    public function tearDown()
    {
        if ($this->server) {
            $this->server->stop();
            $this->app['storage.main.pdo']
                ->exec(file_get_contents(__DIR__ . '/../../schema/main.uninstall.sql'));
            chdir($this->dir);
        }
        parent::tearDown();
    }
}
