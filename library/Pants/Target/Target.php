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

use Pants\Task\AbstractTask;
use Pants\Task\Tasks;

/**
 * Target
 *
 * @package Pants\Target
 */
class Target extends AbstractTask
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
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        $this->tasks = new Tasks();
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
     * @return Target
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
     * @return Target
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
     * @return Target
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
     * @return Target
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
     * @return Target
     */
    public function setName($name)
    {
        $this->name = $name;
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
        $this->unless = $unless;
        return $this;
    }

}
