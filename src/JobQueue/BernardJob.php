<?php
namespace Api\JobQueue;

use Bernard\Message\DefaultMessage;

class BernardJob implements JobInterface
{
    /**
     * @var DefaultMessage
     */
    private $message;

    public function __construct(DefaultMessage $message)
    {
        $this->message = $message;
    }

    public function getName(): string
    {
        return $this->message->getName();
    }

    public function getArguments(): array
    {
        return $this->message->all();
    }
}
