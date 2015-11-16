<?php
use Mapper\UserMapper;
use Test\FunctionalTestCase;
use Test\MandrillMessagesLogger;

/**
 * @backupGlobals disabled
 */
class BasicAccountWorkflowTest extends FunctionalTestCase
{
    /**
     * Successful registration by email
     */
    public function testSuccessfulRegistration()
    {
        /**
         * @var $messages MandrillMessagesLogger
         */
        $messages = $this->app['email.mandrill.messages'];

        $client = $this->createApiClient();
        $client->callRegister('sasha@pushkin.ru', 'dantes kozel', 'Alexander', 'Pushkin');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('Content-Type'));

        // Check the email has been sent
        preg_match('#https://.*#', $messages->messages[0]['text'], $matches);
        $url = $matches[0];

        $client->request(
            'GET',
            $url
        );

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertStringStartsWith('text/html', $client->getResponse()->headers->get('Content-Type'));
        $this->assertEquals('UTF-8', $client->getResponse()->getCharset());
        $this->assertContains('Account has been created.', $client->getResponse()->getContent());
    }

    /**
     * Successful login by email
     */
    public function testLoginByEmail()
    {
        $email = 'test@example.com';
        $password = 'pew-pew';
        $first = 'Alexander';
        $last = 'Pushkin';
        $pic = 'https://pushkin.ru/pic.jpg';

        /** @var UserMapper $userMapper */
        $userMapper = $this->app['mapper.user'];
        $userMapper->createUser([
            'email' => $email,
            'password' => $password,
            'first_name' => $first,
            'last_name' => $last,
            'picture' => $pic,
        ]);
        $client = $this->createApiClient();

        $client->callLoginByEmail($email, $password);

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('Content-Type'));
        $response = $client->getJson();
        $this->assertArrayHasKey('token', $response);
        $token = $response['token'];

        // Check the token is valid for subsequent calls
        $client->setAuthToken($token);
        $client->callUser();
        $this->assertEquals('application/json', $client->getResponse()->headers->get('Content-Type'));
        $response = $client->getJson();
        $this->assertEquals(
            [
                'email' => $email,
                'firstName' => $first,
                'lastName' => $last,
                'picture' => $pic,
            ],
            $response);
    }
}
