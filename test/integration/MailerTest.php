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
            'users' => 2,
            'travels' => 10,
            'delta_users' => -1,
            'delta_travels' => "+5"
        ];
        $test = $this;
        $this->mailer->method('send')->willReturnCallback(function (Swift_Message $msg) use ($test) {
            $test->assertEquals(
            "    HopTrip Statistic - ".date('F d, Y')."\n\n    Users: 2 (-1)\n    Travels: 10 (+5)\n",
                $msg->getBody()
            );
            $test->assertEquals(
                "HopTrip Statistic",
                $msg->getSubject()
            );
        });

        $this->service->sendStats($stats);
    }
}
