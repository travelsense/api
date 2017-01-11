<?php
namespace Api\JobQueue;

class JobProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessorCallsExecutorWithArguments()
    {
        $log = [];
        $processor = new JobProcessor([
            'foo' => function () use (&$log) {
                $log[] = func_get_args();
            },
        ]);

        $args = ['a', 'b', 'c'];
        $job = $this->createMock(JobInterface::class);
        $job->method('getName')->willReturn('foo');
        $job->method('getArguments')->willReturn($args);

        $processor->process($job);
        $this->assertEquals($args, $log[0]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Executor foo not found
     */
    public function testExecutorNotFound()
    {
        $processor = new JobProcessor();
        $job = $this->createMock(JobInterface::class);
        $job->method('getName')->willReturn('foo');
        $processor->process($job);
    }
}
