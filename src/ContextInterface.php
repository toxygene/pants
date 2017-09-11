<?php

namespace Pants;

use Pants\Property\PropertiesInterface;
use Pants\Target\Executor\ExecutorInterface;
use Pants\Target\TargetInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Interface for a context
 *
 * @package Pants
 */
interface ContextInterface extends LoggerAwareInterface
{
    /**
     * Get the currently executing target
     *
     * @return TargetInterface|null
     */
    public function getCurrentTarget();

    /**
     * Get the target executor
     *
     * @return ExecutorInterface
     */
    public function getExecutor(): ExecutorInterface;

    /**
     * Get the logger
     *
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;

    /**
     * Get the properties
     *
     * @return PropertiesInterface
     */
    public function getProperties(): PropertiesInterface;
}
