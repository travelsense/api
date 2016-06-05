<?php
namespace Api\Controller;

use Api\Controller\ClientController;

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
