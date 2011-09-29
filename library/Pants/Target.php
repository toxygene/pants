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

use Pants\Task,
    Pants\Task\AbstractTask,
    Pants\Tasks;

/**
 * Target
 *
 * @package Pants
 */
class Target extends AbstractTask
{

    /**
     * Depends
     * @var array
     */
    protected $_depends = array();

    /**
     * Description
     * @var string
     */
    protected $_description;

    /**
     * Hidden
     * @var boolean
     */
    protected $_hidden = false;

    /**
     * If conditions
     * @var array
     */
    protected $_if = array();

    /**
     * Name
     * @var string
     */
    protected $_name;

    /**
     * Tasks
     * @var Tasks
     */
    protected $_tasks;

    /**
     * Unless conditions
     * @var array
     */
    protected $_unless = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_tasks = new Tasks();
    }

    /**
     * Execute the target
     *
     * @return Target
     */
    public function execute()
    {
        foreach ($this->getDepends() as $depends) {
            $this->getProject()
                 ->execute($depends);
        }

        $properties = $this->getProject()
                           ->getProperties();

        foreach ($this->getIf() as $if) {
            if (!isset($properties->$if) || !$properties->$if) {
                return $this;
            }
        }

        foreach ($this->getUnless() as $unless) {
            if (isset($properties->$unless) || $properties->$unless) {
                return $this;
            }
        }

        foreach ($this->getTasks() as $task) {
            $task->setProject($this->getProject())
                 ->execute();
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
        return $this->_depends;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Get the hidden flag
     *
     * @return boolean
     */
    public function getHidden()
    {
        return $this->_hidden;
    }

    /**
     * Get the if conditionals
     *
     * @return array
     */
    public function getIf()
    {
        return $this->_if;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
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
     * Get the unless conditionals
     *
     * @return array
     */
    public function getUnless()
    {
        return $this->_unless;
    }

    /**
     * Set the depends
     *
     * @param array $depends
     * @return Target
     */
    public function setDepends(array $depends)
    {
        $this->_depends = $depends;
        return $this;
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return Target
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * Set the hidden flag
     *
     * @param boolean $hidden
     * @return Target
     */
    public function setHidden($hidden)
    {
        $this->_hidden = $hidden;
        return $this;
    }

    /**
     * Set the if conditionals
     *
     * @param array $if
     * @return Target
     */
    public function setIf(array $if)
    {
        $this->_if = $if;
        return $this;
    }

    /**
     * Set the name
     *
     * @param string $name
     * @return Target
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Set the unless conditionals
     *
     * @param array $unless
     * @return Target
     */
    public function setUnless(array $unless)
    {
        $this->_unless = $unless;
        return $this;
    }

}
