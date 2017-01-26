<?php
namespace Api;

use PHPUnit_Framework_TestCase;
use Swift_Message;

class MailerTest extends PHPUnit_Framework_TestCase
{
    private $mailer;
    private $service;

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
        $test = $this;
        $this->mailer->method('send')->willReturnCallback(function (Swift_Message $msg) use ($test) {
            $test->assertEquals(
                "HopTrip - January 02, 2017 20:01\nBig trouble,"
                ." we're have a Exception!\n\nMessage: Internal Server Error\nStatus: 500\n",
                $msg->getBody()
            );
            $test->assertEquals(
                "HopTrip - Internal Server Error (Jan 02, 20:01)",
                $msg->getSubject()
            );
        });

        $this->service->sendErrorMessage(
            'Internal Server Error',
            \Symfony\Component\HttpFoundation\Response::HTTP_INTERNAL_SERVER_ERROR,
            new \DateTime('2017-01-02 20:01:01'));
    }
}
