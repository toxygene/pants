<?php


namespace Pants\Task;

use Pants\BuildException as BaseBuildException;
use Pants\Target\TargetInterface;
use Throwable;

class BuildException extends BaseBuildException
{
    /**
     * @var TargetInterface
     */
    private $target;

    /**
     * @var TaskInterface
     */
    private $task;

    /**
     * Constructor
     *
     * @param string $message
     * @param TargetInterface $target
     * @param TaskInterface $task
     * @param Throwable|null $throwable
     */
    public function __construct(
        string $message,
        TargetInterface $target,
        TaskInterface $task,
        Throwable $throwable = null
    ) {
        parent::__construct(
            $message,
            null,
            $throwable
        );

        $this->target = $target;
        $this->task = $task;
    }

    /**
     * Get the target
     *
     * @return TargetInterface
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Get the task
     *
     * @return TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }
}
