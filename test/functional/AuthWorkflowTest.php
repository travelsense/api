<?php
namespace Test;

use Api\Test\FunctionalTestCase;

class AuthWorkflowTest extends FunctionalTestCase
{
    public function testRegisterAndAuth()
    {
        $this->createAndLoginUser();
        $user = $this->apiClient->getCurrentUser();
        $this->assertEquals(
            (object) [
                'firstName' => 'Alexander',
                'lastName' => 'Pushkin',
                'email' => 'sasha@pushkin.ru',
                'picture' => 'http://pushkin.ru/sasha.jpg',
            ],
            $user
        );

    }
}
