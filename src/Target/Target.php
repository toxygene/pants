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
use Pants\Project;
use Pants\Property\Properties;
use Pants\Task\Task;
use Pants\Task\Tasks;

/**
 * Target
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Target
 */
class Target implements Task
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
     * @var Tasks|Task[]
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
    public function execute(Project $project): Task
    {
        foreach ($this->getDepends() as $depends) {
            $project->getTargets()
                ->$depends
                ->execute($project);
        }

        foreach ($this->getIf() as $if) {
            if (!isset($project->getProperties()->$if) || !$project->getProperties()->$if) {
                return $this;
            }
        }

        foreach ($this->getUnless() as $unless) {
            if (isset($project->getProperties()->$unless) || $project->getProperties()->$unless) {
                return $this;
            }
        }

        foreach ($this->tasks as $task) {
            $task->execute($project);
        }

        return $this;
    }

    /**
     * Get the depends
     *
     * @return array
     */
    public function getDepends(): array
    {
        return $this->depends;
    }

    /**
     * Get the description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the hidden flag
     *
     * @return boolean
     */
    public function getHidden()
    {
        return true === $this->hidden;
    }

    /**
     * Get the if conditionals
     *
     * @return string[]
     */
    public function getIf()
    {
        return $this->if;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the tasks
     *
     * @return Tasks|Task[]
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Get the unless conditionals
     *
     * @return string[]
     */
    public function getUnless()
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
