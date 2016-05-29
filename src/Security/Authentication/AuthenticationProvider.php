<?php
namespace Api\Security\Authentication;

use Silex\Application;
use Silex\ServiceProviderInterface;

class AuthenticationProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given app.
     *
     * Expected settings:
     * auth.unsecured_routes - array of route names which do not require auth headers
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['auth.credentials'] = new Credentials();
        $app['auth.authenticator'] = $app->share(
            function ($app) {
                $auth = new UserAuthenticator(
                    $app['auth.credentials'],
                    $app['security.session_manager'],
                    $app['auth.unsecured_routes']
                );
                $auth->setLogger($app['monolog']);
                return $auth;
            }
        );
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     *
     * @param Application $app
     */
    public function boot(Application $app)
    {
        if ($app['auth.enabled']) {
            $app['dispatcher']->addSubscriber($app['auth.authenticator']);
        }
    }
}
