<?php
/**
 * Pants
 *
 * Copyright (c) 2011-2017, Justin Hendrickson
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

namespace Pants\Target;

use JMS\Serializer\Annotation as JMS;
use Pants\BuildException;
use Pants\ContextInterface;
use Pants\Project;
use Pants\Property\Properties;
use Pants\Task\TaskInterface;
use Pants\Task\Tasks;
use Pants\Task\TasksInterface;

/**
 * Standard target
 *
 * @JMS\ExclusionPolicy("all")
 */
class Target implements TargetInterface
{

    /**
     * Depends
     *
     * @JMS\Expose()
     * @JMS\SerializedName("depends")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("array<string>")
     * @JMS\XmlElement(cdata=false)
     * @JMS\XmlList(entry="depends", inline=true)
     *
     * @var string[]
     */
    protected $depends = array();

    /**
     * Description
     *
     * @JMS\Expose()
     * @JMS\SerializedName("description")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string
     */
    protected $description;

    /**
     * Hidden
     *
     * @JMS\Expose()
     * @JMS\SerializedName("hidden")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("boolean")
     *
     * @var boolean
     */
    protected $hidden;

    /**
     * If conditions
     *
     * @JMS\Expose()
     * @JMS\SerializedName("if")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("array<string>")
     * @JMS\XmlElement(cdata=false)
     * @JMS\XmlList(entry="if", inline=true)
     *
     * @var string[]
     */
    protected $if = array();

    /**
     * Name
     *
     * @JMS\Expose()
     * @JMS\SerializedName("name")
     * @JMS\Type("string")
     * @JMS\XmlAttribute()
     *
     * @var string
     */
    protected $name;

    /**
     * Tasks
     *
     * @JMS\Expose()
     * @JMS\SerializedName("tasks")
     * @JMS\Type("Pants\Task\Tasks")
     * @JMS\XmlList(entry="task")
     *
     * @var Tasks|TaskInterface[]
     */
    protected $tasks;

    /**
     * Unless conditions
     *
     * @JMS\Expose()
     * @JMS\SerializedName("unless")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("array<string>")
     * @JMS\XmlElement(cdata=false)
     * @JMS\XmlList(entry="unless", inline=true)
     *
     * @var string[]
     */
    protected $unless = array();

    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->tasks = new Tasks();
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TargetInterface
    {
        foreach ($this->getDepends() as $depends) {
            $context->getLogger()->info(
                'executing dependent target',
                [
                    'target' => $this->getName(),
                    'depends' => $depends
                ]
            );

            $context->getExecutor()
                ->executeSingle($depends, $context);
        }

        foreach ($this->getIf() as $if) {
            $context->getLogger()->debug(
                'checking if property',
                [
                    'target' => $this->getName(),
                    'if' => $if
                ]
            );

            if (!$context->getProperties()->exists($if) || !$context->getProperties()->get($if)) {
                $context->getLogger()->info(
                    'if property not set or false',
                    [
                        'target' => $this->getName(),
                        'if' => $if
                    ]
                );

                return $this;
            }
        }

        foreach ($this->getUnless() as $unless) {
            $context->getLogger()->debug(
                'checking unless property',
                [
                    'target' => $this->getName(),
                    'unless' => $unless
                ]
            );

            if ($context->getProperties()->exists($unless) && $context->getProperties()->get($unless)) {
                $context->getLogger()->info(
                    'unless property set and true',
                    [
                        'target' => $this->getName(),
                        'unless' => $unless
                    ]
                );

                return $this;
            }
        }

        $context->getLogger()->info(
            'executing target tasks',
            [
                'target' => $this->getName()
            ]
        );

        foreach ($this->tasks as $task) {
            try {
                $task->execute($context);
            } catch (BuildException $e) {
                $context->getLogger()->error(
                    $e->getMessage(),
                    [
                        'target' => $this->getName(),
                        'task' => get_class($task) // todo improve
                    ]
                );
            }
        }


        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDepends(): array
    {
        return $this->depends;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function getHidden(): bool
    {
        return true === $this->hidden;
    }

    /**
     * {@inheritdoc}
     */
    public function getIf(): array
    {
        return $this->if;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getTasks(): TasksInterface
    {
        return $this->tasks;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnless(): array
    {
        return $this->unless;
    }

    /**
     * Set the depends
     *
     * @param array $depends
     * @return self
     */
    public function setDepends(array $depends): self
    {
        $this->depends = $depends;
        return $this;
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Set the hidden flag
     *
     * @param boolean $hidden
     * @return self
     */
    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * Set the if conditionals
     *
     * @param string[] $if
     * @return self
     */
    public function setIf(array $if): self
    {
        $this->if = $if;
        return $this;
    }

    /**
     * Set the unless conditionals
     *
     * @param string[] $unless
     * @return self
     */
    public function setUnless(array $unless): self
    {
        $this->unless = $unless;
        return $this;
    }
}
