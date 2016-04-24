<?php
namespace Test;

use Api\Test\FunctionalTestCase;

class AuthWorkflowTest extends FunctionalTestCase
{
    public function testUpdateUserDetails()
    {
        $this->createAndLoginUser();
        $user = $this->apiClient->getCurrentUser();
        $this->assertEquals(
            (object)[
                'id'        => 1,
                'firstName' => 'Alexander',
                'lastName'  => 'Pushkin',
                'email'     => 'sasha@pushkin.ru',
                'picture'   => 'http://pushkin.ru/sasha.jpg',
            ],
            $user
        );

        $this->apiClient->updateUser([
            'id'        => 1,
            'firstName' => 'Natalia',
            'lastName'  => 'Pushkina',
            'picture'   => 'http://pushkin.ru/sasha.jpg',
            'email'     => 'sasha@pushkin.ru',
        ]);
        $user = $this->apiClient->getCurrentUser();
        $this->assertEquals(
            (object)[
                'id'        => 1,
                'firstName' => 'Natalia',
                'lastName'  => 'Pushkina',
                'email'     => 'sasha@pushkin.ru',
                'picture'   => 'http://pushkin.ru/sasha.jpg',
            ],
            $user
        );
    }
}
