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
 */

namespace Pants;

use Pants\Properties,
    Pants\Targets,
    Pants\Task\Chdir,
    Pants\Tasks;

/**
 *
 */
class Project
{

    /**
     * Basedir
     * @var string
     */
    protected $_basedir;

    /**
     * Default task name
     * @var string
     */
    protected $_default;

    /**
     * Properties
     * @var Properties
     */
    protected $_properties;

    /**
     * Targets
     * @var Targets
     */
    protected $_targets;

    /**
     * Tasks
     * @var Tasks
     */
    protected $_tasks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_properties = new Properties();
        $this->_targets    = new Targets();
        $this->_tasks      = new Tasks();
    }

    /**
     * Get the base directory
     *
     * @return string
     */
    public function getBaseDir()
    {
        return $this->_basedir;
    }

    /**
     * Execute targets
     *
     * @param array $targets
     * @return Project
     */
    public function execute($targets = array())
    {
        $this->_setupBaseDir()
             ->_setupBuiltinProperties();

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
     * Get the default target name
     *
     * @return string
     */
    public function getDefault()
    {
        return $this->_default;
    }

    /**
     * Get the properties
     *
     * @return Properties
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * Get the targets
     *
     * @return Targets
     */
    public function getTargets()
    {
        return $this->_targets;
    }

    /**
     * Get the tasks
     *
     * @return Tasks
     */
    public function getTasks()
    {
        return $this->_tasks;
    }

    /**
     * Set the basedir
     *
     * @param string $basedir
     * @return Project
     */
    public function setBaseDir($basedir)
    {
        $this->_basedir = $basedir;
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
        $this->_default = $default;
        return $this;
    }

    /**
     * Setup the basedir
     *
     * @return Project
     */
    protected function _setupBaseDir()
    {
        if ($this->getBaseDir()) {
            chdir($this->getBaseDire());
        }

        return $this;
    }

    /**
     * Setup the builtin properties
     *
     * @return Project
     */
    protected function _setupBuiltinProperties()
    {
        $properties = $this->getProperties();

        $properties->set("host.os", PHP_OS)
                   ->set("pants.version", "@version@")
                   ->set("php.version", PHP_VERSION);

        foreach ($_SERVER as $key => $value) {
            $properties->set("env.{$key}", $value);
        }

        return $this;
    } 

}
