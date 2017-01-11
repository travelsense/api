<?php
namespace Api;

use Api\JobQueue\GenericJob;
use Api\JobQueue\JobInterface;
use Api\JobQueue\JobProcessorInterface;
use Api\JobQueue\JobQueueInterface;
use PHPUnit\Framework\TestCase;

class JobQueueTest extends TestCase
{
    public function testJobQueue()
    {
        $app = new Application('test');
        /** @var JobQueueInterface $queue */
        $queue = $app['job.queue'];

        $job = new GenericJob('test_job', ['foo', 'bar']);
        $processor = $this->createMock(JobProcessorInterface::class);
        $processor
            ->expects($this->once())
            ->method('process')
            ->with($this->callback(function (JobInterface $job) {
                $this->assertEquals('test_job', $job->getName());
                $this->assertEquals(['foo', 'bar'], $job->getArguments());
                return true;
            }));
        $queue->add($job);
        $this->assertEquals(false, $queue->isEmpty());
        $queue->processNext($processor);
        $this->assertEquals(true, $queue->isEmpty());
    }
}
