<?php
namespace Api\Service;

use Swift_Mailer;

class SwiftEmailGeneratorTest extends \PHPUnit_Framework_TestCase
{
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

        $swift_email = new SwiftEmailGenerator('from_test@example.com', 'Test name', ['to_test@example.com']);

        $message = call_user_func($swift_email, $content, $records);

        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals('Exception: Test Exception', $message->getSubject());
        $this->assertEquals('from_test@example.com', key($message->getFrom()));
        $this->assertEquals('to_test@example.com', key($message->getTo()));
    }
}
