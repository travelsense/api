<?php
namespace Api\JobQueue;

use F3\Flock\Lock;

class FrontController
{
    /**
     * @var JobQueueInterface
     */
    private $queue;

    /**
     * @var  JobProcessorInterface
     */
    private $processor;

    /**
     * @var Lock
     */
    private $lock;

    public function __construct(JobQueueInterface $queue, JobProcessorInterface $processor, Lock $lock)
    {
        $this->queue = $queue;
        $this->lock = $lock;
        $this->processor = $processor;
    }

    public function run()
    {
        if (!$this->lock->acquire(Lock::NON_BLOCKING)) {
            return;
        };
        while (!$this->queue->isEmpty()) {
            $this->queue->processNext($this->processor);
        }
        $this->lock->release();
    }
}
