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

use Pale\Pale;
use Pants\BuildException;
use Pants\Property\Properties;
use Pants\Task\Task;

/**
 * Symlink file task
 *
 * @package Pants\Task
 */
class Symlink implements Task
{

    /**
     * Properties
     *
     * @var Properties
     */
    protected $properties;

    /**
     * Source file
     *
     * @var string
     */
    protected $source;
    
    /**
     * Target file
     *
     * @var string
     */
    protected $target;

    /**
     * Constructor
     *
     * @param Properties $properties
     */
    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    /**
     * Execute the task
     *
     * @return self 
     * @throws BuildException
     */
    public function execute()
    {
        if (!$this->getSource()) {
            throw new BuildException("Source not set");
        }

        if (!$this->getTarget()) {
            throw new BuildException("Target not set");
        }

        $source = $this->filterProperties($this->getSource());
        $target = $this->filterProperties($this->getTarget());

        Pale::run(function() use ($source, $target) {
            // symlink
        });

        return $this;
    }

    /**
     * Get the source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Get the target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }
    
    /**
     * Set the source
     *
     * @param string $source
     * @return self
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Set the target 
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
