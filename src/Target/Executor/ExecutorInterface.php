<?php

namespace Pants\Target\Executor;

use Pants\ContextInterface;
use Pants\Target\TargetInterface;

/**
 * Interface for a target executor
 */
interface ExecutorInterface
{
    /**
     * Get the current target
     *
     * @return TargetInterface|null
     */
    public function getCurrentTarget();

    /**
     * Execute multiple targets
     *
     * @param string[] $targetNames
     * @param ContextInterface $context
     * @return ExecutorInterface
     */
    public function executeMultiple(array $targetNames, ContextInterface $context): self;

    /**
     * Execute a single target
     *
     * @param string $targetName
     * @param ContextInterface $context
     * @return ExecutorInterface
     */
    public function executeSingle(string $targetName, ContextInterface $context): self;
}
