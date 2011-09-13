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

namespace Pants\Task;

use Docblox_Parser as Parser,
    Pants\BuildException;

/**
 * Docblox
 *
 * @package Pants
 * @subpackage Task
 */
class Docblox extends AbstractTask
{

    /**
     * Force documentation
     * @var boolean
     */
    protected $_force;

    /**
     * Markers
     * @var array
     */
    protected $_markers;

    /**
     * Target
     * @var string
     */
    protected $_target;

    /**
     * Template
     * @var string
     */
    protected $_template;

    /**
     * Title
     * @var string
     */
    protected $_title;

    /**
     * Validate
     * @var boolean
     */
    protected $_validate;

    /**
     * Execute the task
     *
     * @return Docblox
     * @throw BuildException
     */
    public function execute()
    {
        $task = new DocBlox_Task_Project_Run();

        if ($this->getForce()) {
            $task->setForce($this->getForce());
        }

        if ($this->getMarkers()) {
            $task->setMarkers(implode(",", $this->getMarkers()));
        }

        if ($this->getTarget()) {
            $task->setTarget($this->getTarget());
        }

        if ($this->getTitle()) {
            $task->setTitle($this->getTitle());
        }

        if ($this->getValidate()) {
            $task->setValidate($this->getValidate());
        }

        $task->execute();

        $transform = new DocBlox_Task_Project_Transform();

        if ($task->getTarget()) {
            $transform->setTarget($task->getTarget());
        }

        if ($this->getTemplate()) {
            $transform->setTemplate($this->getTemplate());
        }

        $transform->execute();

        return $this;
    }

    /**
     * Get the force full documentation flag
     *
     * @return boolean
     */
    public function getForce()
    {
        return $this->_force;
    }

    /**
     * Get the markers
     *
     * @return array
     */
    public function getMarkers()
    {
        return $this->_markers;
    }

    /**
     * Get the target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->_target;
    }

    /**
     * Get the template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Get the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set the force full documentation flag
     *
     * @param boolean $force
     * @return Docblox
     */
    public function setForce($force)
    {
        $this->_force = $force;
        return $this;
    }

    /**
     * Set the markers
     *
     * @param array $markers
     * @return Docblox
     */
    public function setMarkers(array $markers)
    {
        $this->_markers = $Markers;
        return $this;
    }

    /**
     * Set the target
     *
     * @param string $target
     * @return Docblox
     */
    public function setTarget($target)
    {
        $this->_target = $target;
        return $this;
    }

    /**
     * Set the template
     *
     * @param string $template
     * @return Docblox
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
        return $this;
    }

    /**
     * Set the title
     *
     * @param string $title
     * @return Docblox
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

}
