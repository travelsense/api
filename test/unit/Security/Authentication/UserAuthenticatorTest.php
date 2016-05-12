<?php
namespace Api\Security\Authentication;

use Api\Security\SessionManager;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

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

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var GetResponseEvent
     */
    private $event;

    public function setUp()
    {
        $this->credentials = $this->getMock('\\Api\\Security\\Authentication\\Credentials');
        $this->sessionManager = $this->getMockBuilder('\\Api\\Security\\SessionManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->event = $this->getMockBuilder('Symfony\\Component\\HttpKernel\\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->setMethods(['getRequest'])
            ->getMock();

        $this->authenticator = new UserAuthenticator($this->credentials, $this->sessionManager, ['excluded']);

        $this->logger = $this->getMock('\\Psr\\Log\\LoggerInterface');
        $this->authenticator->setLogger($this->logger);
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

        $this->event->method('getRequest')->willReturn($request);

        $this->authenticator->onRequest($this->event);

    }

    public function testGetSubscribedEvents()
    {
        $this->assertEquals(['kernel.request' => 'onRequest'], UserAuthenticator::getSubscribedEvents());
    }

    public function testIsExcludedRouteTrue()
    {
        $request = new Request([], [], ['_route' => 'excluded']);

        $this->event->method('getRequest')->willReturn($request);
        
        $this->logger->expects($this->once())->method('info')->with('Route excluded from auth');

        $this->authenticator->onRequest($this->event);
    }

    public function testNotPregMatchFalse()
    {
        $request = new Request([], [], ['_route' => 'secured-route']);
        $request->headers = new HeaderBag(['Authorization' => 'zzz']);

        $this->event->method('getRequest')->willReturn($request);

        $this->authenticator->onRequest($this->event);
    }
}
