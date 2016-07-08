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

$app['security.session_manager'] = function ($app) {
    return new SessionManager($app['mapper.db.sessions']);
};

$app->on(KernelEvents::REQUEST, function (GetResponseEvent $event) use ($app) {
    $request = $event->getRequest();
    $route = $request->attributes->get('_route');
    if (in_array($route, $app['config']['security']['unsecured_routes'])) {
        return;
    }
    $auth_header = $request->headers->get('Authorization');
    if (!preg_match('/^Token (.+)/i', $auth_header, $matches)) {
        $event->setResponse(new Response('', Response::HTTP_UNAUTHORIZED, ['WWW-Authenticate' => 'Token']));
        return;
    }
    $auth_event = new AuthenticationEvent($matches[1]);
    $app['dispatcher']->dispatch($auth_event::NAME, $auth_event);
});

$app->on(AuthenticationEvent::NAME, function (AuthenticationEvent $event) use ($app) {
    $user_id = $app['security.session_manager']->getUserId($event->getToken());
    if (empty($user_id)) {
        throw new ApiException('Invalid token', ApiException::INVALID_TOKEN);
    }
    $app['security.credentials']->setUserId($user_id);
});

$app['user'] = function ($app) {
    /** @var \Api\Security\Credentials $credentials */
    $credentials = $app['security.credentials'];
    $id = $credentials->getUserId();
    if (empty($id)) {
        throw new LogicException('User not authenticated');
    }
    /** @var \Api\Mapper\DB\UserMapper $user_mapper */
    $user_mapper = $app['mapper.db.user'];
    return $user_mapper->fetchById($id);
};
