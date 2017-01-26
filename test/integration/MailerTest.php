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
                "Message: Invalid Argument\nCode: 0\n\n"
                ."InvalidArgumentException: Invalid Argument in /vagrant/test/integration/MailerTest.php:120\n"
                ."Stack trace:\n"
                ."#0 [internal function]: Api\MailerTest-&gt;testErrorMessage()\n"
                ."#1 /vagrant/vendor/phpunit/phpunit/src/Framework/TestCase.php(1111): "
                ."ReflectionMethod-&gt;invokeArgs(Object(Api\MailerTest), Array)\n"
                ."#2 /vagrant/vendor/phpunit/phpunit/src/Framework/TestCase.php(962): "
                ."PHPUnit_Framework_TestCase-&gt;runTest()\n"
                ."#3 /vagrant/vendor/phpunit/phpunit/src/Framework/TestResult.php(709): "
                ."PHPUnit_Framework_TestCase-&gt;runBare()\n"
                ."#4 /vagrant/vendor/phpunit/phpunit/src/Framework/TestCase.php(917): "
                ."PHPUnit_Framework_TestResult-&gt;run(Object(Api\MailerTest))\n"
                ."#5 /vagrant/vendor/phpunit/phpunit/src/Framework/TestSuite.php(728): "
                ."PHPUnit_Framework_TestCase-&gt;run(Object(PHPUnit_Framework_TestResult))\n"
                ."#6 /vagrant/vendor/phpunit/phpunit/src/Framework/TestSuite.php(728): "
                ."PHPUnit_Framework_TestSuite-&gt;run(Object(PHPUnit_Framework_TestResult))\n"
                ."#7 /vagrant/vendor/phpunit/phpunit/src/Framework/TestSuite.php(728): "
                ."PHPUnit_Framework_TestSuite-&gt;run(Object(PHPUnit_Framework_TestResult))\n"
                ."#8 /vagrant/vendor/phpunit/phpunit/src/TextUI/TestRunner.php(487): "
                ."PHPUnit_Framework_TestSuite-&gt;run(Object(PHPUnit_Framework_TestResult))\n"
                ."#9 /vagrant/vendor/phpunit/phpunit/src/TextUI/Command.php(188): "
                ."PHPUnit_TextUI_TestRunner-&gt;doRun(Object(PHPUnit_Framework_TestSuite), Array, true)\n"
                ."#10 /vagrant/vendor/phpunit/phpunit/src/TextUI/Command.php(118): "
                ."PHPUnit_TextUI_Command-&gt;run(Array, true)\n"
                ."#11 /vagrant/vendor/phpunit/phpunit/phpunit(52): PHPUnit_TextUI_Command::main()\n"
                ."#12 {main}\n",
                $msg->getBody()
            );
            $test->assertEquals(
                "HopTrip Error: Invalid Argument (code: 0)",
                $msg->getSubject()
            );
        });

        $this->service->sendErrorMessage( new \InvalidArgumentException('Invalid Argument'));
    }
}
