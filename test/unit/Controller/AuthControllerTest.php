<?php
namespace Api\Controller;

use Api\Exception\ApiException;
use Api\Mapper\DB\SessionMapper;
use Api\Mapper\DB\UserMapper;
use Api\Model\User;
use Api\Security\SessionManager;
use Api\Service\ImageLoader;
use Api\Service\ImageSaver;
use Api\Test\ControllerTestCase;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphPicture;
use Facebook\GraphNodes\GraphUser;
use Hackzilla\PasswordGenerator\Generator\PasswordGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthControllerTest extends ControllerTestCase
{
    private $user_mapper;
    private $session_manager;
    private $facebook;
    private $pw_gen;
    private $img_loader;
    private $img_saver;
    private $request;

    /**
     * @var AuthController
     */
    private $controller;

    /**
     *
     */
    public function setUp()
    {
        $this->user_mapper = $this->getMockBuilder(UserMapper::class)
            ->setMethods(['fetchByEmailAndPassword', 'insert', 'fetchByEmail'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->user_mapper->method('fetchByEmailAndPassword')
            ->willReturnMap([
                ['user1@example.com', '123', $this->buildUser()],
                ['notfound@example.com', '123', null],
            ]);

        $this->session_manager = $this->getMockBuilder(SessionManager::class)
            ->setMethods(['createSession'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->facebook = $this->getMockBuilder(Facebook::class)
            ->setMethods(['setDefaultAccessToken', 'get', 'getGraphUser'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->img_loader = $this->getMockBuilder(ImageLoader::class)
            ->setMethods(['upload', 'save'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->img_saver = $this->getMockBuilder(ImageSaver::class)
            ->setMethods(['save'])
            ->disableOriginalConstructor()
            ->getMock();

        $user_pic = $this->getMockBuilder(GraphPicture::class)
            ->setMethods(['getUrl'])
            ->getMock();
        
        $user_pic->method('getUrl')->willReturn('https://avatarko.ru/img/kartinka/13/kot_ochki_12879.jpg');

        $fb_user = $this->getMockBuilder(GraphUser::class)
            ->setMethods(['getFirstName', 'getLastName', 'getPicture', 'getEmail'])
            ->getMock();
        foreach ([
                     'getEmail'     => 'sasha@pushkin.ru',
                     'getFirstName' => 'Alexander',
                     'getLastName'  => 'Pushkin',
                 ] as $method => $value) {
            $fb_user->method($method)->willReturn($value);
        }

        $this->facebook->method('get')
            ->with('/me?fields=picture,email,first_name,last_name')
            ->willReturnSelf();

        $this->facebook->method('getGraphUser')
            ->willReturn($fb_user);

        $fb_user->method('getPicture')
            ->willReturn($user_pic);

        $this->pw_gen = $this->getMockBuilder(PasswordGeneratorInterface::class)
            ->setMethods(['generatePassword'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->pw_gen->method('generatePassword')->willReturn('test_generated_password');

        $this->request = $this->getMockBuilder(Request::class)
            ->setMethods(['getContent'])
            ->getMock();

        $this->controller = new AuthController(
            $this->user_mapper,
            $this->session_manager,
            $this->facebook,
            $this->pw_gen,
            $this->img_loader,
            $this->img_saver
        );
    }

    public function testCreateByEmailSuccess()
    {
        $this->session_manager->method('createSession')
            ->with(1, $this->request)
            ->willReturn('token1');
        $this->request
            ->method('getContent')
            ->willReturn(json_encode([
                'email'    => 'user1@example.com',
                'password' => '123',
            ]));

        $response = $this->controller->create($this->request);
        $this->assertEquals(["token" => "token1"], $response);
    }

    public function testCreateByEmail404()
    {
        $this->request
            ->method('getContent')
            ->willReturn(json_encode([
                'email'    => 'notfound@example.com',
                'password' => '123',
            ]));
        try {
            $this->controller->create($this->request);
            $this->fail();
        } catch (ApiException $e) {
            $this->assertEquals(ApiException::INVALID_EMAIL_PASSWORD, $e->getCode());
        }
    }

    public function testCreateByFacebookForExistingUser()
    {
        $this->user_mapper->method('fetchByEmail')
            ->with('sasha@pushkin.ru')
            ->willReturn($this->buildUser());

        $this->session_manager->method('createSession')
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
        $this->assertEquals(["token" => "token1"], $response);
    }

    public function testCreateTokenByFacebookForNewUser()
    {
        $this->user_mapper->method('fetchByEmail')
            ->with('sasha@pushkin.ru')
            ->willReturn(null);

        $this->user_mapper->method('insert')
            ->with($this->callback(function (User $user) {
                return $user->getFirstName() === 'Alexander'
                && $user->getEmail() === 'sasha@pushkin.ru'
                && $user->getPicture() === 'https://avatarko.ru/img/kartinka/13/kot_ochki_12879.jpg';
            }))
            ->will($this->returnCallback(function (User $user) {
                $user->setId(42);
            }));

        $this->session_manager->method('createSession')
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
        $this->assertEquals(["token" => "token42"], $response);
    }
}
