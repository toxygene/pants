<?php
/**
 * Pants
 *
 * Copyright (c) 2011-2018, Justin Hendrickson
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * The names of its contributors may not be used to endorse or promote
 *       products derived from this software without specific prior written
 *       permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 'AS IS'
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Justin Hendrickson <justin.hendrickson@gmail.com>
 */

namespace Pants;

use JMS\Serializer\Annotation as JMS;
use Pants\Property\Properties;
use Pants\Property\PropertiesInterface;
use Pants\Target\Executor\Executor;
use Pants\Target\Targets;
use Pants\Target\TargetsInterface;
use Pants\Task\TaskInterface;
use Pants\Task\Tasks;
use Pants\Task\TasksInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * Standard project
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\XmlNamespace(uri="http://www.github.com/toxygene/pants")
 * @JMS\XmlRoot(name="project")
 *
 * @package Pants
 */
class Project implements ProjectInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * Properties
     *
     * @JMS\Expose()
     * @JMS\SerializedName("properties")
     * @JMS\Type("Pants\Property\Properties")
     *
     * @var PropertiesInterface
     */
    protected $properties;

    /**
     * Targets
     *
     * @JMS\Expose()
     * @JMS\SerializedName("targets")
     * @JMS\Type("Pants\Target\Targets")
     * @JMS\XmlList(entry="target")
     *
     * @var TargetsInterface
     */
    protected $targets;

    /**
     * Tasks
     *
     * @JMS\Expose()
     * @JMS\SerializedName("tasks")
     * @JMS\Type("Pants\Task\Tasks")
     * @JMS\XmlList(entry="task")
     *
     * @var TasksInterface|TaskInterface[]
     */
    protected $tasks;

    /**
     * Constructor
     *
     * @param Properties $properties
     * @param Targets $target
     * @param Tasks $tasks
     */
    public function __construct(
        Properties $properties,
        Targets $target,
        Tasks $tasks
    )
    {
        $this->logger = new NullLogger();

        $this->properties = $properties;
        $this->targets = $target;
        $this->tasks = $tasks;
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
    public function execute(array $targets = []): ProjectInterface
    {
        $this->setup();

        $this->logger
            ->info(
                'executing tasks'
            );

        $context = new Context(
            $this->properties,
            new Executor($this->targets),
            $this->logger
        );

        foreach ($this->tasks as $task) {
            $task->execute($context);
        }

        if (empty($targets)) {
            $this->logger
                ->info(
                    'no targets specified'
                );

            if (!$this->properties->exists(PropertiesInterface::DEFAULT_TARGET_NAME)) {
                $this->logger
                    ->warning(
                        'no default target set, bailing out'
                    );

                return $this;
            }

            $targets = [
                $this->properties
                    ->get(PropertiesInterface::DEFAULT_TARGET_NAME)
            ];
        }

        $this->logger
            ->info(
                'executing targets',
                $targets
            );

        $context->getExecutor()
            ->executeMultiple($targets, $context);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetDescriptions(): array
    {
        // todo add project description?
        return $this->targets
            ->getDescriptions();
    }

    /**
     * {@inheritdoc}
     */
    protected function setup(): ProjectInterface
    {
        $builtinProperties = [
            'basedir' => getcwd(),
            'host.os' => PHP_OS,
            'pants.version' => '@version@',
            'php.version' => PHP_VERSION
        ];

        foreach ($_SERVER as $key => $value) {
            $builtinProperties["env.{$key}"] = $value;
        }

        $this->logger
            ->info(
                'setting builtin properties'
            );

        $this->logger
            ->debug(
                'builtin properties',
                $builtinProperties
            );

        $this->properties
            ->merge($builtinProperties);

        return $this;
    }
}
