<?php

namespace Pants\Target;

use Pants\BuildException;

/**
 * Target cycle detected exception
 */
class TargetCycleException extends BuildException
{
    /**
     * Constructor
     *
     * @param TargetInterface $target
     * @param TargetInterface[] $targetsStack
     *
     * @todo
     */
    public function __construct(TargetInterface $target, array $targetsStack)
    {
        parent::__construct();
    }
}
