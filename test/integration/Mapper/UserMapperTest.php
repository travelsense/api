<?php
use Test\DBTestCase;

class UserMapperTest extends DBTestCase
{
    /**
     * @var Mapper\UserMapper;
     */
    private $mapper;

    protected function loadFixture()
    {
        $this->getPdo('main')
            ->exec(file_get_contents(__DIR__.'/UserMapperTest.sql'));
    }

    protected function setUp()
    {
        parent::setUp();
        $this->mapper = new Mapper\UserMapper($this->getPdo('main'));
    }

    public function testHasEmail()
    {
        $this->assertTrue($this->mapper->hasEmail('test@testov.com'));
        $this->assertFalse($this->mapper->hasEmail('non@existing.com'));
    }
}
