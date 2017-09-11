<?php
/**
 * Pants
 *
 * Copyright (c) 2014-2017, Justin Hendrickson
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
use Pants\Task\Call;
use Pants\Task\TaskInterface;
use Pants\Task\Tasks;
use Pants\Task\TasksInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
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
     */
    public function __construct()
    {
        $this->logger = new NullLogger();
        $this->properties = new Properties();
        $this->targets = new Targets();
        $this->tasks = new Tasks();
    }

    /**
     * Get the logger
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Get the properties
     *
     * @return PropertiesInterface
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetDescriptions(): array
    {
        return $this->getTargets()
            ->getDescriptions();
    }

    /**
     * Get the targets
     *
     * @return TargetsInterface
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * Get the tasks
     *
     * @return TasksInterface|TaskInterface[]
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $targets = []): ProjectInterface
    {
        $this->setup();

        $this->getLogger()->info(
            'executing tasks'
        );

        $context = new Context(
            $this->getProperties(),
            new Executor($this->getTargets()),
            $this->getLogger()
        );

        foreach ($this->getTasks() as $task) {
            $task->execute($context);
        }

        if (empty($targets)) {
            $this->getLogger()->info(
                'no targets specified'
            );

            if (!$this->getProperties()->exists(PropertiesInterface::DEFAULT_TARGET_NAME)) {
                $this->getLogger()->warning(
                    'no default target set, bailing out'
                );

                return $this;
            }

            $targets = [
                $this->getProperties()
                    ->get(PropertiesInterface::DEFAULT_TARGET_NAME)
            ];
        }

        $this->getLogger()->info(
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

        $this->getLogger()->info(
            'setting builtin properties'
        );

        $this->getLogger()->debug(
            'builtin properties',
            $builtinProperties
        );

        $this->getProperties()
            ->merge($builtinProperties);

        return $this;
    }
}
