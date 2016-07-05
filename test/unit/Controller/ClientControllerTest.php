<?php
namespace Api\Controller;

use Api\Controller\ClientController;
use Api\Exception\ApiException;

class ClientControllerTest extends \PHPUnit_Framework_TestCase
{
    private $controller;

    public function setUp()
    {
        $this->controller = new ClientController();
    }
    
    /**
     * client
     */
    public function testClient()
    {
        $this->assertEquals(
            [
                'version'   => "0.0.0",
                'supported' => true,
            ],
            $this->controller->version("0.0.0")
        );
    }
     
     /**
      * @expectedException  \Api\Exception\ApiException
      * @expectedExceptionCode \Api\Exception\ApiException::RESOURCE_NOT_FOUND
      */
     public function testInvalidVersion()
     { 
        $this->controller->version(' ');
     }
}
