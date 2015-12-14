<?php
use Mapper\DB\UserMapper;
use Test\FunctionalTestCase;

/**
 * @backupGlobals disabled
 */
class FacebookAccountWorkflowTest extends FunctionalTestCase
{
    private $fb;
    private $fbUser;
    private $fbUserPic;

    public function setUp()
    {
        parent::setUp();

        $this->fb = $this->getMockBuilder('Facebook\\Facebook')
            ->setMethods(['setDefaultAccessToken', 'get', 'getGraphUser'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->app['facebook'] = $this->fb;
        $this->fbUser = $this->getMock(
            'Facebook\\GraphNodes\\GraphUser',
            ['getFirstName', 'getLastName', 'getPicture', 'getEmail']
        );
        $this->fbUserPic = $this->getMock('Facebook\\GraphNodes\\GraphPicture', ['getUrl']);

        $this->fb->method('get')
            ->with('/me?fields=picture,email,first_name,last_name')
            ->willReturnSelf();

        $this->fb->method('getGraphUser')
            ->willReturn($this->fbUser);

        $this->fbUser->method('getPicture')
            ->willReturn($this->fbUserPic);
    }

    /**
     * Successful login by facebook with a new account
     */
    public function testLoginByFacebook()
    {
        foreach(
            [
                'getEmail' => 'sasha@pushkin.ru',
                'getFirstName' => 'Alexander',
                'getLastName' => 'Pushkin',
            ]
            as $method => $value
        ) {
            $this->fbUser->method($method)->willReturn($value);
        }
        $this->fbUserPic->method('getUrl')->willReturn('https://pushkin.ru/pic.jpg');
        $fbToken = 'my test access token yo';
        $this->fb->expects($this->any())
            ->method('setDefaultAccessToken')
            ->with($fbToken);

        $client = $this->createApiClient();
        $client->callLoginFacebook($fbToken);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('Content-Type'));
        $response = $client->getJson();
        $this->assertArrayHasKey('token', $response);

        /** @var UserMapper $userMapper */
        $userMapper = $this->app['mapper.db.user'];

        // Check the user has been created
        $user = $userMapper->fetchByEmail('sasha@pushkin.ru');
        $this->assertEquals('sasha@pushkin.ru', $user->getEmail());
        $this->assertEquals('Alexander', $user->getFirstName());
        $this->assertEquals('Pushkin', $user->getLastName());
        $this->assertEquals('https://pushkin.ru/pic.jpg', $user->getPicture());

        // Trying again, the user should be fetched from the DB
        $client->callLoginFacebook($fbToken);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('Content-Type'));
        $response = $client->getJson();
        $this->assertArrayHasKey('token', $response);

    }

}
