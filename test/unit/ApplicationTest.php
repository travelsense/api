<?php
namespace Api;

use Api\Model\User;
use Symfony\Component\HttpFoundation\Request;

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

    public function testArgumentResolve()
    {
        $user = new User();
        $sec_manager = $this->getMockBuilder('Api\\Security\\SessionManager')
            ->disableOriginalConstructor()
            ->getMock();
        $sec_manager->method('getUserId')
            ->with('xxx')
            ->willReturn(42);

        $user_mapper = $this->getMockBuilder('Api\\Mapper\\DB\\UserMapper')
            ->disableOriginalConstructor()
            ->getMock();

        $user_mapper->method('fetchById')
            ->with(42)
            ->willReturn($user);

        $app = Application::createByEnvironment('test');
        $app['security.session_manager'] = $sec_manager;
        $app['mapper.db.user'] = $user_mapper;
        $args = [];
        $app->get('/test_route', function (User $user, string $foo) use (&$args) {
            $args = func_get_args();
            return '';
        });
        $rq = Request::create('/test_route', Request::METHOD_GET, ['foo' => 'foo_value'], [], [], ['HTTP_AUTHORIZATION' => 'Token xxx']);
        $app->run($rq);
        $this->assertEquals([$user, 'foo_value'], $args);
    }
}
