<?php
namespace Api\Test;


use Api\Controller\ClientController;

class ClientControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @test **/
    public function testClient(){
        $controller = new ClientController();
         
        $this->assertEquals(([
                'version' => "0.0.0",
                'supported' => true,
            ]),$controller->version("0.0.0"));
    }
}
