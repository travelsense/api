<?php
namespace Api\JobQueue;

class JobProcessor implements JobProcessorInterface
{
    private $executors = [];

    public function __construct(array $executors = [])
    {
        foreach ($executors as $name => $callable) {
            $this->setExecutor($name, $callable);
        }
    }

    public function setExecutor(string $name, callable $callback)
    {
        $this->executors[$name] = $callback;
    }

    public function process(JobInterface $job)
    {
        $executor = $this->getExecutor($job->getName());
        $executor(...$job->getArguments());
    }

    protected function getExecutor(string $name): callable
    {
        if (isset($this->executors[$name])) {
            return $this->executors[$name];
        }
        throw new \InvalidArgumentException("Executor $name not found");
    }
}
