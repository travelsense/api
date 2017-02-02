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

    public function testSwiftEmailGenerator()
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
                $test->assertEquals(
                    'HopTrip EMERGENCY: '.get_class($this->e).': '.$this->e->getMessage(),
                    $msg->getSubject()
                );
            });

        $mailStream = new SwiftMailerHandler($this->mailer, $this->swift_email, Logger::EMERGENCY);
        $logger->pushHandler($mailStream);

        $logger->emergency($this->e, ['exception' => $this->e]);
    }

    public function testEmailGenerator()
    {
        $e = new \Exception('Test Exception');
        $records = [
            0 => [
                'message' => "Exception: Test Exception",
                'context' => [
                    'exception' => $e
                ],
                'level' => 600,
                'level_name' => "EMERGENCY",
                'channel' => "api",
                'formatted' => "[2017-02-02 07:24:53] api.EMERGENCY: Exception: Test Exception"
            ]
        ];
        $content = "[2017-02-02 07:24:53] api.EMERGENCY: Exception: Test Exception";

        $this->swift_email = $this->getMockBuilder(SwiftEmailGenerator::class)
            ->setConstructorArgs(['from_test@example.com', 'Test name', ['to_test@example.com']])
            ->setMethods(['_invoke'])
            ->getMock();
        $message = call_user_func($this->swift_email, $content, $records);
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals('HopTrip EMERGENCY: Exception: Test Exception', $message->getSubject());
        $this->assertEquals('from_test@example.com', key($message->getFrom()));
        $this->assertEquals('to_test@example.com', key($message->getTo()));
    }
}
