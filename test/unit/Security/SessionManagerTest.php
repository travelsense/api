<?php
namespace Api\Security;

use Symfony\Component\HttpFoundation\Request;

class SessionManagerTest extends \PHPUnit_Framework_TestCase
{
    private $mapper;
    private $sessionManager;

    public function setUp()
    {
        $this->mapper = $this->getMockBuilder('Api\\Mapper\\DB\\SessionMapper')
            ->disableOriginalConstructor()
            ->setMethods(['createSession', 'getUserId'])
            ->getMock();

        $this->sessionManager = new SessionManager($this->mapper);
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
        $this->assertRegExp('/^[0-9a-f]{40}42$/', $this->sessionManager->createSession(123, $request));
    }

    public function testShortKey()
    {
        $this->assertNull($this->sessionManager->getuserId('foo'));
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
        $this->assertEquals('test_user_id', $this->sessionManager->getuserId($token . '42'));
    }
}
