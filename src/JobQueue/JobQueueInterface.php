<?php
namespace Api\JobQueue;

interface JobQueueInterface
{
    public function add(JobInterface $job);
    public function isEmpty(): bool;
    public function processNext(JobProcessorInterface $processor);
}
