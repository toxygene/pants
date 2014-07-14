<?php
/**
 * Pants
 *
 * Copyright (c) 2014, Justin Hendrickson
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

use Pale\Pale;
use Pants\Property\Properties;
use Pants\Target\Targets;
use Pants\Task\Tasks;

/**
 * Project
 *
 * @package Pants
 */
class Project
{

    /**
     * Base directory
     *
     * @var string
     */
    protected $baseDirectory;

    /**
     * Default task name
     *
     * @var string
     */
    protected $default;

    /**
     * Properties
     *
     * @var Properties
     */
    protected $properties;

    /**
     * Targets
     *
     * @var Targets
     */
    protected $targets;

    /**
     * Tasks
     *
     * @var Tasks
     */
    protected $tasks;

    /**
     * Constructor
     *
     * @param Properties $properties
     * @param Targets $targets
     */
    public function __construct(Properties $properties, Targets $targets, Tasks $tasks)
    {
        $this->properties = $properties;
        $this->targets    = $targets;
        $this->tasks      = $tasks;
    }

    /**
     * Execute targets
     *
     * @param array $targets
     * @return Project
     */
    public function execute($targets = array())
    {
        $this->setupBaseDirectory()
            ->setupBuiltinProperties();

        foreach ($this->getTasks() as $task) {
            $task->execute();
        }

        if (!$targets) {
            if (!$this->getDefault()) {
                return $this;
            }

            $targets = array($this->getDefault());
        } else {
            $targets = (array) $targets;
        }

        foreach ($targets as $target) {
            $this->getTargets()
                ->$target
                ->execute();
        }

        return $this;
    }

    /**
     * Get the base directory
     *
     * @return string
     */
    public function getBaseDirectory()
    {
        return $this->baseDirectory;
    }

    /**
     * Get the default target name
     *
     * @return string
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
     * @return Tasks
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Set the base directory
     *
     * @param string $baseDirectory
     * @return Project
     */
    public function setBaseDirectory($baseDirectory)
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
    public function setDefault($default)
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Setup the base directory
     *
     * @return Project
     */
    protected function setupBaseDirectory()
    {
        if ($this->getBaseDirectory()) {
            $project = $this;
            Pale::run(function() use ($project) {
                return chdir($project->getBaseDirectory());
            });
        }

        return $this;
    }

    /**
     * Setup the builtin properties
     *
     * @return Project
     */
    protected function setupBuiltinProperties()
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
