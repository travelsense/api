<?php
/**
 * Created by PhpStorm.
 * User: f3ath
 * Date: 11/9/15
 * Time: 9:33 PM
 */

namespace Security;


class SessionManagerTest extends \PHPUnit_Framework_TestCase
{
    private $mapper;
    private $tokenManager;
    private $sessionManager;

    public function setUp()
    {
        $this->mapper = $this->getMockBuilder('Mapper\\SessionMapper')
            ->disableOriginalConstructor()
            ->setMethods(['createSession', 'getUserId'])
            ->getMock();

        $this->tokenManager = $this->getMockBuilder('Security\\TokenManager')
            ->disableOriginalConstructor()
            ->setMethods(['encrypt', 'decrypt'])
            ->getMock();

        $this->sessionManager = new SessionManager($this->mapper, $this->tokenManager);

    }

    public function testCreateSession()
    {
        $this->mapper
            ->method('createSession')
            ->with(
                'test_user',
                $this->isType('int'),
                'test_device'
            )
            ->willReturn('session_id');
        $this->tokenManager
            ->method('encrypt')
            ->with($this->matchesRegularExpression('/^session_id\.\d+/'))
            ->willReturn('encrypted_token');
        $this->assertEquals('encrypted_token', $this->sessionManager->createSession('test_user', 'test_device'));

    }

    public function testGetuserId()
    {
        $this->mapper
            ->method('getUserId')
            ->with(
                'session_id',
                42
            )
            ->willReturn('test_user_id');
        $this->tokenManager
            ->method('decrypt')
            ->with('encrypted_token')
            ->willReturn('session_id.42');
        $this->assertEquals('test_user_id', $this->sessionManager->getuserId('encrypted_token'));

    }
}
