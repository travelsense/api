<?php
/**
 * @var $app Api\Application
 */

use Api\Exception\ApiException;
use Api\Security\Access\AccessManager;
use Api\Security\SessionManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

$app['security.session_manager'] = function ($app) {
    return new SessionManager($app['mapper.db.sessions']);
};

$app['security.access_manager'] = function ($app) {
    return new AccessManager(
        $app['mapper.db.user_role']
    );
};

$app['user'] = null;

$app->on(KernelEvents::REQUEST, function (GetResponseEvent $event) use ($app) {
    $request = $event->getRequest();
    $route = $request->attributes->get('_route');
    $auth_header = $request->headers->get('Authorization');
    if (preg_match('/^Token (.+)/i', $auth_header, $matches)) {
        $user_id = $app['security.session_manager']->getUserId($matches[1]);
        if (empty($user_id)) {
            throw new ApiException('Invalid token', ApiException::INVALID_TOKEN);
        }
        $app['user'] = $app['mapper.db.user']->fetchById($user_id);
    } elseif (!in_array($route, $app['config']['security']['unsecured_routes'])) {
        $event->setResponse(new Response('', Response::HTTP_UNAUTHORIZED, ['WWW-Authenticate' => 'Token']));
    }
});
