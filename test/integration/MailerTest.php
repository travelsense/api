<?php
namespace Api;

use PHPUnit_Framework_TestCase;
use Swift_Message;

class MailerTest extends PHPUnit_Framework_TestCase
{
    private $mailer;
    private $service;
    private $e;

    public function setUp()
    {
        $app = new Application('test');

        $app['mailer'] = $this->mailer = $this->getMockBuilder('Swift_Mailer')
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();

        $this->service = $app['email.service'];
    }

    public function testConfirmation()
    {
        $test = $this;
        $this->mailer->method('send')->willReturnCallback(function (Swift_Message $msg) use ($test) {
            $test->assertEquals(
                "Please follow the link to confirm your account: https://example.com/email/confirm/xxx\n",
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
        $this->mailer->method('send')->willReturnCallback(function (Swift_Message $msg) use ($test) {
            $test->assertEquals(
                "Please follow the link to reset your password: https://example.com/password/reset/xxx\n",
                $msg->getBody()
            );
            $test->assertEquals(
                "Password reset link",
                $msg->getSubject()
            );
        });

        $this->service->sendPasswordResetLink('user@examle.com', 'xxx');
    }

    public function testSendStats()
    {
        $stats = [
            'users'         => 2,
            'travels'       => 10,
            'delta_users'   => -1,
            'delta_travels' => "+5",
        ];
        $test = $this;
        $this->mailer->method('send')->willReturnCallback(function (Swift_Message $msg) use ($test) {
            $test->assertEquals(
                "HopTrip Stats - January 02, 2017\n\nUsers: 2 (-1)\nTravels: 10 (+5)\n",
                $msg->getBody()
            );
            $test->assertEquals(
                "HopTrip stats Jan 02 - U: 2 (-1), T: 10 (+5)",
                $msg->getSubject()
            );
        });

        $this->service->sendStats($stats, new \DateTime('2017-01-02'));
    }

    public function testErrorMessage()
    {
        $this->e = new \Exception('Test Exception');
        $test = $this;
        $this->mailer->method('send')->willReturnCallback(function (Swift_Message $msg) use ($test) {
            $test->assertEquals(
                "Message: Test Exception\nCode: 0\n\n".$this->e->__toString()."\n",
                str_replace('&gt;', '>', $msg->getBody())
            );
            $test->assertEquals(
                "HopTrip Error: Test Exception (code: 0)",
                $msg->getSubject()
            );
        });

        $this->service->sendErrorMessage( $this->e );
    }
}
