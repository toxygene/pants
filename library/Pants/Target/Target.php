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

use Pants\Property\Properties;
use Pants\Target\Targets;
use Pants\Task\Task;
use Pants\Task\Tasks;

/**
 * Target
 *
 * @package Pants\Target
 */
class Target implements Task
{

    /**
     * Depends
     *
     * @var array
     */
    protected $depends = array();

    /**
     * Description
     *
     * @var string
     */
    protected $description;

    /**
     * Hidden
     *
     * @var boolean
     */
    protected $hidden = false;

    /**
     * If conditions
     *
     * @var array
     */
    protected $if = array();

    /**
     * Name
     *
     * @var string
     */
    protected $name;
    
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
     * Unless conditions
     *
     * @var array
     */
    protected $unless = array();

    /**
     * Constructor
     *
     * @param Targets $targets
     * @param Properties $properties
     * @param Tasks $tasks
     */
    public function __construct(Targets $targets, Properties $properties, Tasks $tasks)
    {
        $this->targets    = $targets;
        $this->properties = $properties;
        $this->tasks      = $tasks;
    }

    /**
     * Execute the target
     *
     * @return self
     */
    public function execute()
    {
        foreach ($this->getDepends() as $depends) {
            $this->getTargets()
                ->$depends
                ->execute();
        }

        foreach ($this->getIf() as $if) {
            if (!isset($this->getProperties()->$if) || !$this->getProperties()->$if) {
                return $this;
            }
        }

        foreach ($this->getUnless() as $unless) {
            if (isset($this->getProperties()->$unless) || $this->getProperties()->$unless) {
                return $this;
            }
        }

        foreach ($this->getTasks() as $task) {
            $task->execute();
        }

        return $this;
    }

    /**
     * Get the depends
     *
     * @return array
     */
    public function getDepends()
    {
        return $this->depends;
    }

    /**
     * Get the description
     *
     * @return string
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
        return $this->hidden;
    }

    /**
     * Get the if conditionals
     *
     * @return array
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
     * @return selfs
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
     * Get the unless conditionals
     *
     * @return array
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
    public function setDepends(array $depends)
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
    public function setDescription($description)
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
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * Set the if conditionals
     *
     * @param array $if
     * @return self
     */
    public function setIf(array $if)
    {
        $this->if = $if;
        return $this;
    }

    /**
     * Set the name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set properties
     *
     * @param Properties $properties
     * @return self
     */
    public function setProperties(Properties $properties)
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * Set the targets
     *
     * @param Targets $targets
     * @return self
     */
    public function setTargets(Targets $targets)
    {
        $this->targets = $targets;
        return $this;
    }

    /**
     * Set the unless conditionals
     *
     * @param array $unless
     * @return self
     */
    public function setUnless(array $unless)
    {
        $this->unless = $unless;
        return $this;
    }

}
