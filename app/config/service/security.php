<?php
/**
 * @var $app Api\Application
 */

use Api\Exception\ApiException;
use Api\Security\AuthenticationEvent;
use Api\Security\Credentials;
use Api\Security\SessionManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

$app['security.credentials'] = function () {
    return new Credentials();
};

$app['security.session_manager'] = function($app) {
    return new SessionManager($app['mapper.db.sessions']);
};

$app->on(KernelEvents::REQUEST, function(GetResponseEvent $event) use ($app) {
    $request = $event->getRequest();
    $route = $request->attributes->get('_route');
    if (in_array($route, $app['config']['security']['unsecured_routes'])) {
        return;
    }
    $authHeader = $request->headers->get('Authorization');
    if (!preg_match('/^Token (.+)/i', $authHeader, $matches)) {
        $event->setResponse(new Response('', Response::HTTP_UNAUTHORIZED, ['WWW-Authenticate' => 'Token']));
        return;
    }
    $authEvent = new AuthenticationEvent($matches[1]);
    $app['dispatcher']->dispatch($authEvent::NAME, $authEvent);
});

$app->on(AuthenticationEvent::NAME, function(AuthenticationEvent $event) use ($app) {
    $userId = $app['security.session_manager']->getUserId($event->getToken());
    if (empty($userId)) {
        throw new ApiException('Invalid token', ApiException::INVALID_TOKEN);
    }
    $app['security.credentials']->setUserId($userId);
});

$app['user'] = function($app) {
    /** @var \Api\Security\Credentials $credentials */
    $credentials = $app['security.credentials'];
    $id = $credentials->getUserId();
    if (empty($id)) {
        throw new LogicException('User not authenticated');
    }
    /** @var \Api\Mapper\DB\UserMapper $userMapper */
    $userMapper = $app['mapper.db.user'];
    return $userMapper->fetchById($id);
};

