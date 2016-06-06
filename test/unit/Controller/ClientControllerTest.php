<?php
namespace Api\Controller;

use Api\Controller\ClientController;
use Api\Exception\ApiException;
use phpunit\framework\TestCase;

class ClientControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testClient()
    {
        $controller = new ClientController();
        $this->assertEquals(
            [
                'version' => "0.0.0",
                'supported' => true,
            ],
            $controller->version("0.0.0")
        );
    }
}

class ClientExeptionTest extends ApiException
{
    /**
     * @expectedException
     */
     public function testException()
     { 
        $controller1 = new ClientController();
        $this->expectException('Unknown version', $controller1->version(' '));
     }
}

