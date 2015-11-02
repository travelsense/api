<?php
use Test\FunctionalTestCase;
use Test\MandrillMessagesLogger;

/**
 * User: f3ath
 * Date: 11/1/15
 * Time: 4:36 PM
 */
class UserRegistrationTest extends FunctionalTestCase
{
    public $token = null;

    public function testSuccessfulRegistration()
    {
        /**
         * @var $messages MandrillMessagesLogger
         */
        $messages = $this->app['email.mandrill.messages'];

        $json = json_encode([
            'email' => 'alexander@pushkin.ru',
            'password' => 'dantes kozel',
        ]);

        $client = $this->createClient();
        $client->request(
            'POST',
            'https://example.com/user/register-by-email',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            $json
        );

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('Content-Type'));

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

}
