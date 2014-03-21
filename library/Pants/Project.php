<?php
/**
 * Pants
 *
 * Copyright (c) 2011, Justin Hendrickson
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
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
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
use Pants\Type\Types;

/**
 * Project
 *
 * @package Pants
 */
class Project
{

    /**
     * Basedir
     *
     * @var string
     */
    protected $baseDir;

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
     * Types
     *
     * @var Types
     */
    protected $types;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->properties = new Properties();
        $this->targets    = new Targets();
        $this->tasks      = new Tasks();
        $this->types      = new Types();
    }

    /**
     * Execute targets
     *
     * @param array $targets
     * @return Project
     */
    public function execute($targets = array())
    {
        $this->setupBaseDir()
             ->setupBuiltinProperties();

        foreach ($this->getTasks() as $task) {
            $task->setProject($this)
                 ->execute();
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
                 ->{$target}
                 ->setProject($this)
                 ->execute();
        }

        return $this;
    }

    /**
     * Get the base directory
     *
     * @return string
     */
    public function getBaseDir()
    {
        return $this->baseDir;
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
     * Get the types
     *
     * @return Types
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set the base directory
     *
     * @param string $baseDir
     * @return Project
     */
    public function setBaseDir($baseDir)
    {
        $this->baseDir = $baseDir;
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
    protected function setupBaseDir()
    {
        if ($this->getBaseDir()) {
            $project = $this;
            Pale::run(function() use ($project) {
                return chdir($project->getBaseDir());
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
