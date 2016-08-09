<?php
namespace Api\Security;

use Api\Mapper\DB\SessionMapper;
use Symfony\Component\HttpFoundation\Request;

class SessionManagerTest extends \PHPUnit_Framework_TestCase
{
    private $mapper;
    private $manager;

    public function setUp()
    {
        $this->mapper = $this->getMockBuilder(SessionMapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['createSession', 'getUserId'])
            ->getMock();

        $this->manager = new SessionManager($this->mapper);
    }

    public function testCreateSession()
    {
        $this->mapper
            ->method('createSession')
            ->with(
                123,
                $this->anything(),
                'test_device'
            )
            ->willReturn(42);
        $request = new Request([], [], [], [], [], ['HTTP_USER_AGENT' => 'test_device']);
        $this->assertRegExp('/^[0-9a-f]{40}42$/', $this->manager->createSession(123, $request));
    }

    public function testShortKey()
    {
        $this->assertNull($this->manager->getuserId('foo'));
    }

    public function testGetUserId()
    {
        $token = sha1('xxx');
        $this->mapper
            ->method('getUserId')
            ->with(
                '42',
                $token
            )
            ->willReturn('test_user_id');
        $this->assertEquals('test_user_id', $this->manager->getuserId($token . '42'));
    }
}
