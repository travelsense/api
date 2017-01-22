<?php
namespace Api;

use Api\Event\UserLoggedWithFacebook;
use Api\Job\UpdateAvatar;
use Api\JobQueue\JobQueueInterface;
use PHPUnit\Framework\TestCase;

class EventsTest extends TestCase
{
    public function testEvents()
    {
        $app = new Application('test');
        $queue = $this->createMock(JobQueueInterface::class);
        $app['job.queue'] = $queue;
        $queue
            ->expects($this->once())
            ->method('add')
            ->with($this->callback(function (UpdateAvatar $job) {
                $this->assertEquals([42, 'http://example.com'], $job->getArguments());
                return true;
            }));

        $app['dispatcher']->dispatch(
            UserLoggedWithFacebook::NAME,
            new UserLoggedWithFacebook(42, 'http://example.com')
        );
    }
}
