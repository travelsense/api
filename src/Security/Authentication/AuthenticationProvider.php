<?php
/**
 * Created by PhpStorm.
 * User: f3ath
 * Date: 11/8/15
 * Time: 6:15 PM
 */

namespace Security\Authentication;

use Security\SessionManager;
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
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['auth.credentials'] = new Credentials();
        $app['auth.authenticator'] = $app->share(function($app) {
            return new UserAuthenticator(
                $app['auth.credentials'],
                $app['security.session_manager'],
                $app['auth.unsecured_routes']
            );
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     * @param Application $app
     */
    public function boot(Application $app)
    {
        if ($app['auth.enabled']) {
            $app['dispatcher']->addSubscriber($app['auth.authenticator']);
        }
    }
}