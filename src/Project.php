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
use Pants\Target\Targets;
use Pants\Task\Task;
use Pants\Task\Tasks;

/**
 * Project
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\XmlNamespace(uri="http://www.github.com/toxygene/pants")
 * @JMS\XmlRoot(name="project")
 *
 * @package Pants
 */
class Project
{

    /**
     * Base directory
     *
     * @JMS\Expose()
     * @JMS\SerializedName("base_directory")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlAttribute()
     *
     * @var string
     */
    protected $baseDirectory;

    /**
     * Default task name
     *
     * @JMS\Expose()
     * @JMS\SerializedName("default")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlAttribute()
     *
     * @var string
     */
    protected $default;

    /**
     * Properties
     *
     * @JMS\Expose()
     * @JMS\SerializedName("properties")
     * @JMS\Type("Pants\Property\Properties")
     *
     * @var Properties
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
     * @var Targets
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
     * @var Tasks|Task[]
     */
    protected $tasks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->properties = new Properties();
        $this->targets    = new Targets();
        $this->tasks      = new Tasks();
    }

    /**
     * Execute targets
     *
     * @param array $targets
     * @return self
     */
    public function execute($targets = array()): self
    {
        $this->setupBaseDirectory()
            ->setupBuiltinProperties();

        foreach ($this->getTasks() as $task) {
            $task->execute($this);
        }

        if (empty($targets)) {
            if (!$this->getDefault()) {
                return $this;
            }

            $targets = [$this->getDefault()];
        }

        foreach ($targets as $target) {
            $this->getTargets()
                ->$target
                ->execute($this);
        }

        return $this;
    }

    /**
     * Get the base directory
     *
     * @return string|null
     */
    public function getBaseDirectory()
    {
        return $this->baseDirectory;
    }

    /**
     * Get the default target name
     *
     * @return string|null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Get the properties
     *
     * @return Properties
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Get the targets
     *
     * @return Targets
     */
    public function getTargets()
    {
        return $this->targets;
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
     * Set the base directory
     *
     * @param string $baseDirectory
     * @return self
     */
    public function setBaseDirectory(string $baseDirectory): self
    {
        $this->baseDirectory = $baseDirectory;
        return $this;
    }

    /**
     * Set the default target name
     *
     * @param string $default
     * @return Project
     */
    public function setDefault(string $default): self
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Setup the base directory
     *
     * @return self
     */
    protected function setupBaseDirectory(): self
    {
        if ($this->getBaseDirectory()) {
            chdir($this->getBaseDirectory());
        }

        return $this;
    }

    /**
     * Setup the builtin properties
     *
     * @return self
     */
    protected function setupBuiltinProperties(): self
    {
        $properties = $this->getProperties();

        $properties->basedir           = getcwd();
        $properties->{"host.os"}       = PHP_OS;
        $properties->{"pants.version"} = "@version@";
        $properties->{"php.version"}   = PHP_VERSION;

        foreach ($_SERVER as $key => $value) {
            $properties->{"env.{$key}"} = $value;
        }

        return $this;
    }
}
