<?php
namespace Api\Controller;

use Api\Exception\ApiException;

class ClientControllerTest extends ClientController
{
    public function ClientTest(){
        $a = new ClientController();
        $b = $a->version("0.0.0");
        $this->assertEquals("0.0.0", $b->version([
                'version' => "0.0.0",
                'supported' => true,
            ]));
    }
}
