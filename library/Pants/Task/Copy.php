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

/**
 * Copy file(s) task
 *
 * @package Pants\Task
 */
class Copy implements Task
{

    /**
     * Destination file
     *
     * @var string
     */
    protected $destination;

    /**
     * Target file
     *
     * @var string
     */
    protected $file;

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
     * @return Copy
     * @throws BuildException
     */
    public function execute()
    {
        if (!$this->getFile()) {
            throw new BuildException('File is not set');
        }

        if (!$this->getDestination()) {
            throw new BuildException('Destination is not set');
        }

        $file        = $this->getProperties()->filter($this->getFile());
        $destination = $this->getProperties()->filter($this->getDestination());

        Pale::run(function() use($file, $destination) {
            return copy($file, $destination);
        });

        return $this;
    }

    /**
     * Get the destination
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Get the target file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
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
     * Set the destination file
     *
     * @param string $destination
     * @return Copy
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * Set the target file
     *
     * @param string $file
     * @return Copy
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

}
