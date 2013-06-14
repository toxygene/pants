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

use BadMethodCallException;
use ErrorException;
use Closure;
use Pants\BuildException;
use Pants\Project;
use Pants\Property\PropertyNameCycleException;
use Pants\Task\Task;
use Pale\Pale;

/**
 * Abstract base task
 *
 * @package Pants
 * @subpackage Task
 */
abstract class AbstractTask implements Task
{

    /**
     * Project
     *
     * @var Project
     */
    protected $project;

    /**
     * Constructor
     *
     * @param array|Traversable $options
     * @throws BadMethodCallException
     */
    public function __construct($options = array())
    {
        foreach ($options as $key => $value) {
            $method = "set" . $key;
            if (!method_exists($this, $method)) {
                throw new BadMethodCallException("Method '{$method}' does not exist");
            }
            $this->$method($value);
        }
    }

    /**
     * Filter a string for any properties
     *
     * @param string $string
     * @return string
     * @throws BuildException
     */
    public function filterProperties($string)
    {
        if (!$this->getProject()) {
            throw new BuildException("Project not set");
        }

        try {
            return $this->getProject()
                        ->getProperties()
                        ->filter($string);
        } catch(PropertyNameCycleException $e) {
            throw new BuildException("An error occurred while filtering the property", null, $e);
        }
    }

    /**
     * Get the project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Run a function
     *
     * @param Closure $function
     * @return mixed
     * @throws BuildException
     */
    protected function run(Closure $function)
    {
        try {
            return Pale::run($function);
        } catch (ErrorException $e) {
            throw new BuildException($e->getMessage(), null, $e);
        }
    }

    /**
     * Set the project
     *
     * @param Project $project
     * @return Task
     */
    public function setProject(Project $project)
    {
        $this->project = $project;
        return $this;
    }

}
