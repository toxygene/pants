<?php

namespace Pants;

use Pants\Property\PropertiesInterface;
use Pants\Target\Executor\ExecutorInterface;
use Pants\Target\TargetInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Standard context
 */
class Context implements ContextInterface
{
    use LoggerAwareTrait;

    /**
     * @var TargetInterface|null
     */
    protected $currentTarget;

    /**
     * @var ExecutorInterface
     */
    protected $executor;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var PropertiesInterface
     */
    protected $properties;

    /**
     * Constructor
     *
     * @param PropertiesInterface $properties
     * @param ExecutorInterface $executor
     * @param LoggerInterface $logger
     */
    public function __construct(
        PropertiesInterface $properties,
        ExecutorInterface $executor,
        LoggerInterface $logger
    )
    {
        $this->properties = $properties;
        $this->executor = $executor;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentTarget()
    {
        return $this->executor
            ->getCurrentTarget();
    }

    /**
     * {@inheritdoc}
     */
    public function getExecutor(): ExecutorInterface
    {
        return $this->executor;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties(): PropertiesInterface
    {
        return $this->properties;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
