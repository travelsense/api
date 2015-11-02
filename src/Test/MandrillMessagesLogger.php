<?php
/**
 * User: f3ath
 * Date: 11/1/15
 * Time: 6:14 PM
 */

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