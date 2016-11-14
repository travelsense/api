<?php
namespace Api;

use PHPUnit_Framework_TestCase;

class ServicesTest extends PHPUnit_Framework_TestCase
{
    public function servicesToTest()
    {
        return [
            ['controller.user'],
            ['controller.auth'],
            ['controller.travel'],
            ['controller.uber'],
            ['controller.wego'],
            ['controller.health'],
        ];
    }

    /**
     * Make sure the major services can be instantiated
     * @param string $service
     * @dataProvider servicesToTest
     */
    public function testServices(string $service)
    {
        foreach (['prod', 'dev', 'test', 'stage'] as $env) {
            $app = new Application($env);
            $this->assertNotEmpty($app[$service]);
        }
    }
}
