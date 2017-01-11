<?php
namespace Api;

use PHPUnit\Framework\TestCase;

class CronJobsTest extends TestCase
{
    public function testExecutables()
    {
        $script = __DIR__ . '/../../app/cron/job_queue.php';
        exec("php $script", $output, $code);
        $this->assertEquals([], $output);
        $this->assertEquals(0, $code);
    }
}
