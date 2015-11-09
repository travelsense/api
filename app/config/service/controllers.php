<?php
/**
 * Controllers
 * @var $app Application
 */

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app['controller.activity'] = $app->share(function($app) {
    return new Controller\Activity($app['storage.main.pdo']);
});

$app['controller.user'] = $app->share(function($app) {
    return new Controller\UserController(
        $app['mapper.user'],
        $app['email.mailer'],
        $app['security.token_manager'],
        $app['security.session_manager'],
        $app['auth.credentials']
    );
});
