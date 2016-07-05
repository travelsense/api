<?php
namespace Api;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testConfig()
    {
        $app = new Application(
            [
                'foo' => [
                    'bar' => 'baz',
                ],
            ]
        );
        $this->assertEquals('baz', $app['config']['foo']['bar']);

    }

    public function testSecureConfig()
    {
        $config = [
            'foo'         => [
                'bar' => 'baz',
            ],
            'secure_json' => __DIR__ . '/ApplicationTest/secure.json',
        ];

        $app = new Application($config);
        $this->assertEquals('secret', $app['config']['foo']['bar']);
    }

    public function testServices()
    {
        $app = new Application([
            'service' => [
                __DIR__ . '/ApplicationTest/service.php',
            ],
        ]);
        $this->assertEquals('my service', $app['my.service']);
    }
}
