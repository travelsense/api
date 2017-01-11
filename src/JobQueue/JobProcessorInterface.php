<?php
namespace Api\JobQueue;

interface JobProcessorInterface
{
    public function process(JobInterface $job);
}
