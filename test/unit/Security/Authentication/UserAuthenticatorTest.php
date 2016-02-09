<?php
namespace Security\Authentication;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;

class UserAuthenticatorTest extends \PHPUnit_Framework_TestCase
{
    private $credentials;
    private $sessionManager;
    /**
     * @var UserAuthenticator
     */
    private $authenticator;

    public function setUp()
    {
        $this->credentials = $this->getMock('Security\\Authentication\\Credentials');
        $this->sessionManager = $this->getMockBuilder('Security\\SessionManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->authenticator = new UserAuthenticator($this->credentials, $this->sessionManager, ['excluded']);
    }

    /**
     * @expectedException \Exception\ApiException
     * @expectedExceptionMessage Invalid or expired auth token
     * @expectedExceptionCode 202
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

}
