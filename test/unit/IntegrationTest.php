<?php

class IntegrationTest extends PHPUnit_Framework_TestCase
{
    public function servicesToTest()
    {
        return [
            ['controller.api.user'],
            ['controller.api.auth'],
            ['controller.api.travel'],
            ['controller.api.uber'],
            ['controller.api.wego'],
        ];
    }

    /**
     * Make sure the major services can be instantiated
     * @param string $service
     * @dataProvider servicesToTest
     */
    public function testServices($service)
    {
        $app = Application::createByEnvironment('test');

        $this->assertNotEmpty($app[$service]);
    }

}
