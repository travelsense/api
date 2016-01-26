<?php
namespace Security\Authentication;

use Security\AuthenticationException;
use Security\Exception\AuthorizationException;
use Security\SessionManager;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class UserAuthenticator implements EventSubscriberInterface
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
     * @var array
     */
    private $excludedRoutes = [];

    public function __construct(Credentials $credentials, SessionManager $sessionManager, array $excludedRoutes)
    {
        $this->credentials = $credentials;
        $this->sessionManager = $sessionManager;
        $this->excludedRoutes = $excludedRoutes;
    }

    /**
     * @param $route
     * @return bool
     */
    private function isExcludedRoute($route)
    {
        return in_array($route, $this->excludedRoutes);
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        if ($this->isExcludedRoute($route)) {
            return;
        }

        $authHeader = $request->headers->get('Authorization');
        if (! preg_match('/^Token (.+)/i', $authHeader, $matches)) {
            $event->setResponse(new Response('', 401, ['WWW-Authenticate' => 'Token']));
            return;
        }
        $token = $matches[1];
        $userId = $this->sessionManager->getUserId($token);
        if (null === $userId) {
            throw new AuthenticationException('Invalid token');
        }
        $this->credentials->setUser($userId);
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onRequest',
        ];
    }
}