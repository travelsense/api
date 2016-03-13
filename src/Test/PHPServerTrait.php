<?php
namespace Api\Test;

use LogicException;
use Weew\HttpServer\HttpServer;

trait PHPServerTrait
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
     * @var int
     */
    protected $port = 8888;

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
            $this->server = new HttpServer($this->host, $this->port, 'app.php');
        }
        if ($this->server->isRunning()) {
            throw new LogicException('Server is already running');
        }
        $this->dir = getcwd();
        chdir(__DIR__ . '/../../public');
        $this->server->disableOutput();
        $this->server->start();
    }

    /**
     * stop the php dev server
     */
    public function stopServer()
    {
        if ($this->server && $this->server->isRunning()) {
            $this->server->stop();
            chdir($this->dir);
        } else {
            throw new LogicException('Server is not running');
        }
    }
}
