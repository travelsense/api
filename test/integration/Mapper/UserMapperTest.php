<?php

use Test\DBTesting;

class UserMapperTest extends PHPUnit_Framework_TestCase
{
    use DBTesting;
    /**
     * @var Mapper\UserMapper;
     */
    private $mapper;

    /**
     * @var PDO
     */
    private $pdo;

    protected function setUp()
    {
        parent::setUp();
        $this->pdo = new PDO('pgsql:dbname=vaca_test', 'vaca', 'vaca');
        $this->setUpDatabase($this->pdo, 'main');
        $this->pdo
            ->exec(file_get_contents(__DIR__.'/UserMapperTest.sql'));
        $this->mapper = new Mapper\UserMapper($this->pdo);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->tearDownDatabase($this->pdo, 'main');
    }

    public function testHasEmail()
    {
        $this->assertTrue($this->mapper->hasEmail('test@testov.com'));
        $this->assertFalse($this->mapper->hasEmail('non@existing.com'));
    }
}
