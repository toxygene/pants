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

namespace Pants\Task;

use InvalidArgumentException;
use Pants\BuildException;
use Pants\Property\Properties;
use Pants\Target\Targets;

/**
 * Call another target task
 *
 * @package Pants\Task
 */
class Call implements Task
{
    
    /**
     * Properties
     *
     * @var Properties
     */
    protected $properties;

    /**
     * Name of target to call
     *
     * @var string
     */
    protected $target;
    
    /**
     * Targets
     *
     * @var Targets
     */
    protected $targets;
    
    /**
     * Constructor
     *
     * @param Properties $properties
     * @param Targets $targets
     */
    public function __construct(Properties $properties, Targets $targets)
    {
        $this->properties = $properties;
        $this->targets    = $targets;
    }

    /**
     * Execute the task
     *
     * @return self
     * @throws BuildException
     */
    public function execute()
    {
        if (!$this->getTarget()) {
            throw new BuildException('No target set');
        }
        
        $target = $this->getProperties()
            ->filter($this->getTarget());
        
        $this->getTargets()
            ->$target
            ->execute();

        return $this;
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
     * Get the name of the target to call
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
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
     * Set the name of the target to call
     *
     * @param string $target
     * @return self
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

}
