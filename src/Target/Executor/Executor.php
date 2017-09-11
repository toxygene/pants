<?php

namespace Pants\Target\Executor;

use Pants\ContextInterface;
use Pants\Target\TargetCycleException;
use Pants\Target\TargetInterface;
use Pants\Target\TargetsInterface;

/**
 * Standard executor
 */
class Executor implements ExecutorInterface
{
    /**
     * @var TargetsInterface
     */
    private $targets;

    /**
     * @var TargetInterface[]
     */
    private $targetsStack = [];

    /**
     * @var TargetInterface[]
     */
    private $executedTargets = [];

    /**
     * Constructor
     *
     * @param TargetsInterface $targets
     */
    public function __construct(TargetsInterface $targets)
    {
        $this->targets = $targets;
    }

    /**
     * Get the current target
     *
     * @return TargetInterface|null
     */
    public function getCurrentTarget()
    {
        return end($this->targetsStack);
    }

    /**
     * Execute multiple targets
     *
     * @param string[] $targetNames
     * @param ContextInterface $context
     * @return ExecutorInterface
     */
    public function executeMultiple(array $targetNames, ContextInterface $context): ExecutorInterface
    {
        foreach ($targetNames as $targetName) {
            $this->executeSingle($targetName, $context);
        }

        return $this;
    }

    /**
     * Execute a single target
     *
     * @param string $targetName
     * @param ContextInterface $context
     * @return ExecutorInterface
     */
    public function executeSingle(string $targetName, ContextInterface $context): ExecutorInterface
    {
        $target = $this->targets
            ->get($targetName);

        if (in_array($target, $this->targetsStack)) {
            throw new TargetCycleException($target, $this->targetsStack);
        }

        if (in_array($target, $this->executedTargets)) {
            return $this;
        }

        $this->targetsStack[] = $target;

        $target->execute($context);

        $this->executedTargets[] = array_pop($this->targetsStack);

        return $this;
    }
}
