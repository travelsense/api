<?php
namespace Api\Test;

use Api\Application;
use Api\Mapper\DB\BookingMapper;
use Api\Mapper\DB\UserMapper;
use Api\Model\User;
use Api\Security\SessionManager;
use Silex\WebTestCase;

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
        $api = new ApiClient($this->createClient());
        if ($token) {
            $api->setToken($token);
        }
        return $api;
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
