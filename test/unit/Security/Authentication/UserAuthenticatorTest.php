<?php
namespace Api\Security\Authentication;

use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class UserAuthenticatorTest extends PHPUnit_Framework_TestCase
{
    private $credentials;
    private $sessionManager;
    /**
     * @var UserAuthenticator
     */
    private $authenticator;
    private $logger;
    private $event;

    public function setUp()
    {
        $this->credentials = $this->getMock('\\Api\\Security\\Authentication\\Credentials');

        $this->sessionManager = $this->getMockBuilder('\\Api\\Security\\SessionManager')
            ->disableOriginalConstructor()
            ->setMethods(['getUserId'])
            ->getMock();

        $this->logger = $this->getMock('\\Psr\\Log\\LoggerInterface');

        $this->event = $this->getMockBuilder('\\Symfony\\Component\\HttpKernel\\Event\\GetResponseEvent')
            ->disableOriginalConstructor()
            ->setMethods(['getRequest'])
            ->getMock();
    }

    /**
     * @expectedException \Api\Exception\ApiException
     * @expectedExceptionMessage Invalid token
     * @expectedExceptionCode 2200
     */
    public function testOnRequestThrowsException()
    {
        $this->authenticator = new UserAuthenticator($this->credentials, $this->sessionManager, ['excluded']);
        $this->authenticator->setLogger($this->logger);

        $request = new Request([], [], ['_route' => 'secured-route']);
        $request->headers = new HeaderBag(['Authorization' => 'Token zzz']);

        $this->event->method('getRequest')->willReturn($request);

        $this->authenticator->onRequest($this->event);
    }

    public function testOfRequestThrowsException()
    {
        $this->sessionManager->method('getUserId')->willReturn('zzz');//1

        $this->authenticator = new UserAuthenticator($this->credentials, $this->sessionManager, ['excluded']);
        $this->authenticator->setLogger($this->logger);

        $request = new Request([], [], ['_route' => 'secured-route']);
        $request->headers = new HeaderBag(['Authorization' => 'Token zzz']);

        $this->event->method('getRequest')->willReturn($request);

        $this->authenticator->onRequest($this->event);
    }

    public function testIsExcludedRouteTrue()
    {
        $this->authenticator = new UserAuthenticator($this->credentials, $this->sessionManager, ['excluded']);
        $this->authenticator->setLogger($this->logger);

        $request = new Request([], [], ['_route' => 'excluded']);//2
        $request->headers = new HeaderBag(['Authorization' => 'Token zzz']);

        $this->event->method('getRequest')->willReturn($request);

        $this->authenticator->onRequest($this->event);
    }

    public function testNotPregMatchFalse()
    {
        $this->authenticator = new UserAuthenticator($this->credentials, $this->sessionManager, ['excluded']);
        $this->authenticator->setLogger($this->logger);

        $request = new Request([], [], ['_route' => 'secured-route']);
        $request->headers = new HeaderBag(['Authorization' => 'zzz']);//3

        $this->event->method('getRequest')->willReturn($request);

        $this->authenticator->onRequest($this->event);
    }

    public function testGetSubscribedEvents()
    {
        $subscribedEvents=UserAuthenticator::getSubscribedEvents();
    }
}
