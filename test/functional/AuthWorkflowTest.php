<?php
namespace Test;

use Api\Test\FunctionalTestCase;

class AuthWorkflowTest extends FunctionalTestCase
{
    public function testRegisterAndAuth()
    {
        $this->apiClient->registerUser([
            'firstName' => 'Alexander',
            'lastName' => 'Pushkin',
            'email' => 'sasha@pushkin.ru',
            'password' => 'vodka',
        ]);

        $token = $this->apiClient->getTokenByEmail('sasha@pushkin.ru', 'vodka');

        $this->apiClient->setAuthToken($token);

        $user = $this->apiClient->getCurrentUser();

        $this->assertEquals(
            (object) [
                'firstName' => 'Alexander',
                'lastName' => 'Pushkin',
                'email' => 'sasha@pushkin.ru',
                'picture' => '',
            ],
            $user
        );

    }
}
