<?php
namespace Api\Service;

use Api\Application;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Logger;
use Swift_Mailer;
use Swift_Message;

class SwiftEmailGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var SwiftEmailGenerator
     */
    private $swift_email;

    /**
     * @var \Exception
     */
    private $e;

    public function testEmailGenerator()
    {
        $this->e = new \Exception('Test Exception');
        $app = new Application('test');
        $logger = $app['monolog'];

        $this->mailer = $this->getMockBuilder(Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();

        $this->swift_email = new SwiftEmailGenerator('from_test@example.com', 'Test name', ['to_test@example.com']);

        $test = $this;

        $this->mailer->method('send')
            ->willReturnCallback(function (Swift_Message $msg) use ($test) {

                $subj = explode($this->e->getLine(),$this->e->__toString());
                $test->assertEquals(
                    'HopTrip EMERGENCY: ['.date('Y-m-d H:i:s', $msg->getDate()).'] api.EMERGENCY: '
                    .$subj[0].$this->e->getLine(),
                    $msg->getSubject()
                );
            });

        $mailStream = new SwiftMailerHandler($this->mailer, $this->swift_email, Logger::EMERGENCY);
        $logger->pushHandler($mailStream);

        $logger->emergency($this->e);
    }
}
