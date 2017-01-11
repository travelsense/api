<?php
namespace Api\JobQueue;

use Bernard\Envelope;
use Bernard\Message\DefaultMessage;
use Bernard\Queue;

class BernardJobQueue implements JobQueueInterface
{
    /**
     * @var Queue
     */
    private $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function isEmpty(): bool
    {
        return $this->queue->count() === 0;
    }

    public function add(JobInterface $job)
    {
        $this->queue->enqueue(
            new Envelope(
                new DefaultMessage($job->getName(), $job->getArguments())
            )
        );
    }

    public function processNext(JobProcessorInterface $processor)
    {
        $envelop = $this->queue->dequeue();
        if (!$envelop) {
            return;
        }
        $message = $envelop->getMessage();
        if ($message instanceof DefaultMessage) {
            $processor->process(new BernardJob($message));
            $this->queue->acknowledge($envelop);
        } else {
            throw new \InvalidArgumentException(sprintf('invalid message class: %s', get_class($message)));
        }
    }
}
