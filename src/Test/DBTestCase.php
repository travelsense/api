<?php

namespace Test;

use Application;
use PDO;
use PHPUnit_Framework_TestCase;

class DBTestCase extends PHPUnit_Framework_TestCase
{
    protected $databases = ['main'];

    private $app;

    protected function setUp()
    {
        foreach($this->databases as $db) {
            $this->getPdo($db)
                ->exec(file_get_contents(__DIR__."/../../schema/$db.install.sql"));
        }
        $this->loadFixture();
    }

    protected function tearDown()
    {
        foreach($this->databases as $db) {
            $this->getPdo($db)
                ->exec(file_get_contents(__DIR__."/../../schema/$db.uninstall.sql"));
        }
    }

    protected function loadFixture()
    {
    }

    /**
     * @param string $db
     * @return PDO
     */
    public function getPdo($db)
    {
        return $this->getApplication()->offsetGet("storage.$db.pdo");
    }

    /**
     * @return Application
     */
    protected function getApplication()
    {
        if ($this->app === null) {
            $this->app = Application::createByEnvironment('test');
        }
        return $this->app;
    }
}