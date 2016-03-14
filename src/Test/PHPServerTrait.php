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
    protected $wait = 5.0;

    /**
     * @var string
     */
    protected $host = 'localhost';

    /**
     * start the php dev server
     */
    public function startServer()
    {
        if (!$this->server) {
            $this->server = new HttpServer(
                $this->host,
                $this->port,
                __DIR__ . '/../../public/app.php',
                __DIR__ . '/../../public/',
                $this->wait
            );
        }
        if ($this->server->isRunning()) {
            throw new LogicException('Server is already running');
        }
        $this->server->disableOutput();
        $dir = getcwd();
        chdir(__DIR__ . '/../../public/');
        $this->server->start();
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
