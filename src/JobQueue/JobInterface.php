<?php
namespace Api\JobQueue;

interface JobInterface
{
    public function getName(): string;
    public function getArguments(): array;
}
