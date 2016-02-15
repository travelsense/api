<?php

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
