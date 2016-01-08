<?php
namespace Test;


class MandrillMessagesLogger extends \Mandrill_Messages
{
    public function __construct()
    {
    }

    public $messages = [];

    public function send($message, $async = false, $ip_pool = NULL, $send_at = NULL)
    {
        $this->messages[] = $message;
    }

}