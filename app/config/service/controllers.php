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
        $app['mapper.db.user'],
        $app['mapper.json.user'],
        $app['email.mailer'],
        $app['storage.expirable_storage'],
        $app['security.session_manager'],
        $app['auth.credentials'],
        $app['facebook'],
        $app['password_generator']
    );
});
