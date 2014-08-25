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

use Pale\Pale;
use Pants\BuildException;
use Pants\Property\Properties;
use Traversable;

/**
 * Change file(s) owner
 *
 * @package Pants\Task
 */
class Chown implements Task
{

    /**
     * Target files
     *
     * @var array
     */
    protected $files = array();

    /**
     * Owner to set
     *
     * @var string
     */
    protected $owner;

    /**
     * Properties
     *
     * @var Propreties
     */
    protected $properties;
    
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
     * @return Chown
     * @throws BuildException
     */
    public function execute()
    {
        if (!$this->getFiles()) {
            throw new BuildException("Files are not set");
        }

        if (!$this->getOwner()) {
            throw new BuildException("Owner is not set");
        }

        $owner = $this->properties->filter($this->getOwner());

        foreach ($this->getFiles() as $file) {
            $file = $this->properties->filter($file);
            Pale::run(function() use ($file, $owner) {
                return chown($file, $owner);
            });
        }

        return $this;
    }

    /**
     * Get the target files
     *
     * @return string
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Get the owner
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set the target file
     *
     * @param string $file
     * @return self
     */
    public function setFile($file)
    {
        $this->files = array($file);
        return $this;
    }

    /**
     * Set the target files
     *
     * @param Traversable $files
     * @return self
     */
    public function setFiles(Traversable $files)
    {
        $this->files = $files;
        return $this;
    }

    /**
     * Set the owner
     *
     * @param string $owner
     * @return self
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

}
