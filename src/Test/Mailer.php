<?php
namespace Api\Test;

use Swift_Mime_Message;

/**
 * Mail dumper
 *
 * @package Test
 */
class Mailer extends \Swift_Mailer
{
    private $log;

    /**
     * Mailer constructor.
     *
     * @param string $log
     */
    public function __construct($log)
    {
        $this->log = $log;
    }

    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $log = "Date: " . date('Y-m-d H:i:s') ."\n";
        $log .= "Subject: ".$message->getSubject()."\n";
        $log .= $message->getBody()."\n\n";

        file_put_contents($this->log, $log, FILE_APPEND);
    }
}
