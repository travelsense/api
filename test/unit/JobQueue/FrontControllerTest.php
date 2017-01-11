<?php
namespace Api\JobQueue;

use F3\Flock\Lock;

class FrontControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $queue;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $processor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $lock;

    /**
     * @var FrontController
     */
    private $controller;

    protected function setUp()
    {
        $this->queue = $this->createMock(JobQueueInterface::class);
        $this->queue->method('isEmpty')->willReturnOnConsecutiveCalls(false, false, false, true);

        $this->processor = $this->createMock(JobProcessorInterface::class);
        $this->lock = $this->createMock(Lock::class);

        $this->controller = new FrontController($this->queue, $this->processor, $this->lock);
    }

    public function testRunProcessesUntilQueueEmpty()
    {
        $this->lock->expects($this->once())->method('acquire')->willReturn(true);
        $this->lock->expects($this->once())->method('release');
        $this->queue->expects($this->exactly(3))->method('processNext')->with($this->processor);
        $this->controller->run();
    }

    public function testRunExitsIfLockNotAcquired()
    {
        $this->lock->expects($this->once())->method('acquire')->willReturn(false);
        $this->lock->expects($this->never())->method('release');
        $this->queue->expects($this->never())->method('processNext');
        $this->controller->run();
    }
}
