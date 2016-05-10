<?php
namespace Api\Security\Authentication;

use Api\Security\SessionManager;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

class UserAuthenticatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Credentials
     */
    private $credentials;

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var UserAuthenticator
     */
    private $authenticator;

    public function setUp()
    {
        $this->credentials = $this->getMock('\\Api\\Security\\Authentication\\Credentials');
        $this->sessionManager = $this->getMockBuilder('\\Api\\Security\\SessionManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->authenticator = new UserAuthenticator($this->credentials, $this->sessionManager, ['excluded']);
    }

    /**
     * @expectedException \Api\Exception\ApiException
     * @expectedExceptionMessage Invalid token
     * @expectedExceptionCode    2200
     */
    public function testOnRequestThrowsException()
    {
        $request = new Request([], [], ['_route' => 'secured-route']);
        $request->headers = new HeaderBag(['Authorization' => 'Token zzz']);

        $event = $this->getMockBuilder('Symfony\\Component\\HttpKernel\\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->setMethods(['getRequest'])
            ->getMock();
        $event->method('getRequest')->willReturn($request);

        $this->authenticator->onRequest($event);

    }

    public function testGetSubscribedEvents()
    {
        $this->assertEquals(['kernel.request' => 'onRequest_'], UserAuthenticator::getSubscribedEvents());
    }
}
