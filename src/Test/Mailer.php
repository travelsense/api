<?php
namespace Api\Test;

use Swift_Mailer;
use Swift_Mime_Message;

/**
 * Mail dumper
 *
 * @package Test
 */
class Mailer extends Swift_Mailer
{
    private $log;
    private $messages = [];

    /**
     * Mailer constructor.
     *
     * @param string $log
     */
    public function __construct($log)
    {
        $this->log = $log;
    }

    public function send(Swift_Mime_Message $message, &$failed_recipients = null)
    {
        $this->messages[] = $message;
        $log = "Date: " . date('Y-m-d H:i:s') . "\n";
        $log .= "Subject: " . $message->getSubject() . "\n";
        foreach ($message->getChildren() as $mime) {
            $log .= "Mime: " . $mime->getContentType() . "\n";
        }
        $log .= $message->getBody() . "\n\n";

        file_put_contents($this->log, $log, FILE_APPEND);
    }

    /**
     * Get all messages sent through the mailer
     * @return Swift_Mime_Message[]
     */
    public function getLoggedMessages(): array
    {
        return $this->messages;
    }
}
