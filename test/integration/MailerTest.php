<?php

class MailerTest extends PHPUnit_Framework_TestCase
{
    private $mailer;
    private $service;

    public function setUp()
    {
        $app = Application::createByEnvironment('test');

        $app['mailer'] = $this->mailer = $this->getMockBuilder('Swift_Mailer')
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();

        $this->service = $app['email.service'];
    }

    public function testConfirmation()
    {
        $test = $this;
        $this->mailer->method('send')->willReturnCallback(function( Swift_Message $msg) use ($test) {
            $test->assertEquals(
                "Please follow the link to confirm your account: https://travelsen.se/#confirm/xxx\n",
                $msg->getBody()
            );
            $test->assertEquals(
                "Account confirmation",
                $msg->getSubject()
            );
        });

        $this->service->sendAccountConfirmationMessage('user@examle.com', 'xxx');
    }

    public function testPasswordReset()
    {
        $test = $this;
        $this->mailer->method('send')->willReturnCallback(function( Swift_Message $msg) use ($test) {
            $test->assertEquals(
                "Please follow the link to reset your password: https://travelsen.se/#reset/xxx\n",
                $msg->getBody()
            );
            $test->assertEquals(
                "Password reset link",
                $msg->getSubject()
            );
        });

        $this->service->sendPasswordResetLink('user@examle.com', 'xxx');
    }
}
