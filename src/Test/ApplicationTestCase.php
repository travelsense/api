<?php
namespace Api\Test;

use Api\Application;
use Api\Mapper\DB\BookingMapper;
use Api\Mapper\DB\UserMapper;
use Api\Model\User;
use Api\Security\SessionManager;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\ServerRequest;
use HopTrip\ApiClient\ApiClient;
use Psr\Http\Message\RequestInterface;
use Silex\WebTestCase;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\HttpKernel\TerminableInterface;

class ApplicationTestCase extends WebTestCase
{
    /**
     * @return Application
     */
    public function createApplication()
    {
        $app = new Application('test');
        $app['mapper.db.booking'] = $this->createMock(BookingMapper::class);
        $app['mapper.db.user'] = $this->createMock(UserMapper::class);
        $app['security.session_manager'] = $this->createMock(SessionManager::class);
        return $app;
    }

    protected function createApiClient(string $token = null): ApiClient
    {
        $stack = HandlerStack::create(function (RequestInterface $request, array $options) {
            $server_request = (new ServerRequest(
                $request->getMethod(),
                $request->getUri(),
                $request->getHeaders(),
                $request->getBody(),
                $request->getProtocolVersion()
            ))->withQueryParams(\GuzzleHttp\Psr7\parse_query($request->getUri()->getQuery()));
            $factory = new HttpFoundationFactory();
            $diactoros = new DiactorosFactory();
            $symfony_request = $factory->createRequest($server_request);
            $symfony_response = $this->app->handle($symfony_request);
            if ($this->app instanceof TerminableInterface) {
                $this->app->terminate($symfony_request, $symfony_response);
            }
            $response = $diactoros->createResponse($symfony_response);
            $promise = \GuzzleHttp\Promise\promise_for($response);
            return $promise;
        });

        $guzzle = new Client([
            'base_uri' => 'https://localhost',
            'handler' => $stack,
        ]);
        $client = new ApiClient($guzzle);
        $client->setAuthToken($token);
        return $client;
    }

    /**
     * Map tokens to user ids
     * @param array $tokens <token> => <user_id>
     */
    protected function appExpectTokens(array $tokens)
    {
        $this->app['security.session_manager']
            ->method('getUserId')
            ->will(
                $this->returnValueMap(
                    array_map(
                        function (string $a, int $b) {
                            return [$a, $b];
                        },
                        array_keys($tokens),
                        $tokens
                    )
                )
            );
    }

    protected function appExpectUsers(array $users)
    {
        $this->app['mapper.db.user']
            ->method('fetchById')
            ->will(
                $this->returnValueMap(
                    array_map(
                        function (User $user) {
                            return [$user->getId(), $user];
                        },
                        $users
                    )
                )
            );
    }
}
