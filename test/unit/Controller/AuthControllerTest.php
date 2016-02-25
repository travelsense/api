<?php
namespace Api\Controller;

use Api\Exception\ApiException;
use Api\Model\User;
use Api\Test\ControllerTestCase;

class AuthControllerTest extends ControllerTestCase
{
    private $userMapper;
    private $sessionManager;
    private $facebook;
    private $pwGen;
    private $request;

    /**
     * @var AuthController
     */
    private $controller;

    public function setUp()
    {
        $this->userMapper = $this->getMockBuilder('Api\\Mapper\\DB\\UserMapper')
            ->setMethods(['fetchByEmailAndPassword', 'insert','fetchByEmail'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->userMapper->method('fetchByEmailAndPassword')
            ->willReturnMap([
                ['user1@example.com', '123', $this->buildUser()],
                ['notfound@example.com', '123', null],
            ]);

        $this->sessionManager = $this->getMockBuilder('Api\\Security\\SessionManager')
            ->setMethods(['createSession'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->facebook = $this->getMockBuilder('Facebook\\Facebook')
            ->setMethods(['setDefaultAccessToken', 'get', 'getGraphUser'])
            ->disableOriginalConstructor()
            ->getMock();

        $fbUserPic = $this->getMock('Facebook\\GraphNodes\\GraphPicture', ['getUrl']);
        $fbUserPic->method('getUrl')->willReturn('https://pushkin.ru/pic.jpg');

        $fbUser = $this->getMock(
            'Facebook\\GraphNodes\\GraphUser',
            ['getFirstName', 'getLastName', 'getPicture', 'getEmail']
        );
        foreach ([
                'getEmail' => 'sasha@pushkin.ru',
                'getFirstName' => 'Alexander',
                'getLastName' => 'Pushkin',
            ]
        as $method => $value) {
            $fbUser->method($method)->willReturn($value);
        }

        $this->facebook->method('get')
            ->with('/me?fields=picture,email,first_name,last_name')
            ->willReturnSelf();

        $this->facebook->method('getGraphUser')
            ->willReturn($fbUser);

        $fbUser->method('getPicture')
            ->willReturn($fbUserPic);

        $this->pwGen = $this->getMockBuilder('Hackzilla\\PasswordGenerator\\Generator\\PasswordGeneratorInterface')
            ->setMethods(['generatePassword'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->request = $this->getMock('Symfony\\Component\\HttpFoundation\\Request', ['getContent']);

        $this->controller = new AuthController(
            $this->userMapper,
            $this->sessionManager,
            $this->facebook,
            $this->pwGen
        );
    }

    public function testCreateByEmailSuccess()
    {
        $this->sessionManager->method('createSession')
            ->with(1, $this->request)
            ->willReturn('token1');
        $this->request
            ->method('getContent')
            ->willReturn(json_encode([
                'email' => 'user1@example.com',
                'password' => '123',
            ]));

        $response = $this->controller->create($this->request);
        $this->assertEquals('{"token":"token1"}', $response->getContent());

    }

    public function testCreateByEmail404()
    {
        $this->request
            ->method('getContent')
            ->willReturn(json_encode([
                'email' => 'notfound@example.com',
                'password' => '123',
            ]));
        try {
            $this->controller->create($this->request);
            $this->fail();
        } catch (ApiException $e) {
            $this->assertEquals(ApiException::INVALID_EMAIL_PASSWORD, $e->getCode());
            $this->assertEquals(401, $e->getHttpCode());
        }
    }


    public function testCreateByFacebookForExistingUser()
    {
        $this->userMapper->method('fetchByEmail')
            ->with('sasha@pushkin.ru')
            ->willReturn($this->buildUser());

        $this->sessionManager->method('createSession')
            ->with(1, $this->request)
            ->willreturn('token1');

        $this->facebook->expects($this->once())
            ->method('setDefaultAccessToken')
            ->with('test_fb_access_token');

        $this->request
            ->method('getContent')
            ->willReturn(json_encode([
                'fbToken' => 'test_fb_access_token',
            ]));

        $response = $this->controller->create($this->request);
        $this->assertEquals('{"token":"token1"}', $response->getContent());
    }

    public function testCreateTokenByFacebookForNewUser()
    {
        $this->userMapper->method('fetchByEmail')
            ->with('sasha@pushkin.ru')
            ->willReturn(null);

        $this->userMapper->method('insert')
            ->with($this->callback(function (User $user) {
                return $user->getFirstName() === 'Alexander'
                    && $user->getEmail() === 'sasha@pushkin.ru'
                    && $user->getPicture() === 'https://pushkin.ru/pic.jpg';
            }))
            ->will($this->returnCallback(function (User $user) {
                $user->setId(42);
            }));

        $this->sessionManager->method('createSession')
            ->with(42, $this->request)
            ->willreturn('token42');

        $this->facebook->expects($this->once())
            ->method('setDefaultAccessToken')
            ->with('test_fb_access_token');

        $this->request
            ->method('getContent')
            ->willReturn(json_encode([
                'fbToken' => 'test_fb_access_token',
            ]));

        $response = $this->controller->create($this->request);
        $this->assertEquals('{"token":"token42"}', $response->getContent());
    }
}
