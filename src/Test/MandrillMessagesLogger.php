<?php
namespace Test;


class MandrillMessagesLogger extends \Mandrill_Messages
{
    public function __construct()
    {
    }

    public $messages = [];

    public function send($message)
    {
        $this->messages[] = $message;
    }

}