<?php
namespace Api\Controller;

use Api\Exception\ApiException;
use Api\Model\User;
use Api\Test\ControllerTestCase;

class UserControllerTest extends ControllerTestCase
{
    private $userMapper;
    private $mailer;
    private $expStorage;

    /**
     * @var UserController
     */
    private $controller;

    private $testUser;

    public function setUp()
    {
        $this->userMapper = $this->getMockBuilder('Api\\Mapper\\DB\\UserMapper')
            ->setMethods(['insert', 'emailExists'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->mailer = $this->getMockBuilder('Api\\Service\\Mailer\\MailerService')
            ->setMethods(['sendAccountConfirmationMessage'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->expStorage = $this->getMockBuilder('Api\\ExpirableStorage')
            ->setMethods(['store'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = new UserController(
            $this->userMapper,
            $this->mailer,
            $this->expStorage
        );

        $this->testUser = $this->buildUser();
    }

    /**
     * getUser
     */
    public function testGetUser()
    {
        $this->assertEquals(
            [
                'id'        => 1,
                'email'     => 'user1@example.com',
                'picture'   => 'http://example.com/user1.jpg',
                'firstName' => 'User1',
                'lastName'  => 'Tester',
                'created'   => '2000-01-01T00:00:00+00:00',
            ],
            $this->controller->getUser($this->testUser)
        );
    }

    /**
     * createUser
     */
    public function testCreateUser()
    {
        $json = json_encode([
            'email'     => 'test@example.com',
            'password'  => 'my_pass',
            'picture'   => 'http://example.com/user.jpg',
            'firstName' => 'Simple',
            'lastName'  => 'Tester',
        ]);

        $request = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\Request')
            ->setMethods(['getContent'])
            ->getMock();
        
        $request->method('getContent')->willReturn($json);

        $this->userMapper->method('emailExists')
            ->with('test@example.com')
            ->willReturn(true, false);

        $this->userMapper->expects($this->once())
            ->method('insert')
            ->with($this->callback(function (User $u) {
                return $u->getEmail() === 'test@example.com'
                && $u->getFirstName() === 'Simple'
                && $u->getLastName() === 'Tester'
                && $u->getPicture() === 'http://example.com/user.jpg';
            }));

        $this->expStorage->expects($this->once())
            ->method('store')
            ->with('test@example.com')
            ->willReturn('test_token');

        $this->mailer->expects($this->once())
            ->method('sendAccountConfirmationMessage')
            ->with('test@example.com', 'test_token');

        try { // User exists
            $this->controller->createUser($request);
            $this->fail('No exception thrown');
        } catch (ApiException $e) {
            $this->assertEquals(ApiException::USER_EXISTS, $e->getCode());
        }

        $this->assertEquals([], $this->controller->createUser($request));
    }

    public function testCreateUserValidation()
    {
        $json = json_encode([
            'ololo' => 'invalid stuff',
        ]);
            $request = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\Request')
            ->setMethods(['getContent'])
            ->getMock();
        
        
        $request->method('getContent')->willReturn($json);

        try {
            $this->controller->createUser($request);
            $this->fail();
        } catch (ApiException $e) {
            $this->assertEquals(ApiException::VALIDATION, $e->getCode());
        }
    }
}

