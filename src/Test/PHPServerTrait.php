<?php
namespace Api\Test;

use LogicException;
use Weew\HttpServer\HttpServer;

trait PHPServerTrait
{
    /**
     * @var HttpServer
     */
    private $server;

    /**
     * @var int
     */
    protected $port = 8888;

    /**
     * @var float
     */
    protected $wait = 1.0;

    /**
     * @var string
     */
    protected $host = 'localhost';

    /**
     * start the php dev server
     * @param string $log
     */
    public function startServer(string $log = '/dev/null')
    {
        if (!$this->server) {
            $this->server = new HttpServer(
                $this->host,
                $this->port,
                'app.php',
                $this->wait
            );
        }
        if ($this->server->isRunning()) {
            throw new LogicException('Server is already running');
        }
        $this->server->setLogFile($log);
        $dir = getcwd();
        chdir(__DIR__ . '/../../public/');
        $this->server->start();
        sleep(1); // Warming up for too long. See Issue #66
        chdir($dir);
    }

    /**
     * stop the php dev server
     */
    public function stopServer()
    {
        if ($this->server && $this->server->isRunning()) {
            $this->server->stop();
        } else {
            throw new LogicException('Server is not running');
        }
    }
}
