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

namespace Pants\Task;

use Pants\BuildException,
    Pants\Project,
    Pants\Task;

/**
 *
 */
abstract class AbstractTask implements Task
{

    /**
     * Project
     * @var Project
     */
    protected $_project;

    /**
     * Filter a string for any properties
     *
     * @param string $string
     * @return string
     * @throw BuildException
     */
    public function filterProperties($string)
    {
        if (!$this->getProject()) {
            throw new BuildException("The project has not been set");
        }

        try {
            return $this->getProject()
                        ->getProperties()
                        ->filter($string);
        } catch(Exception $e) {
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
        return $this->_project;
    }

    /**
     * Set the project
     *
     * @param Project $project
     * @return Task
     */
    public function setProject(Project $project)
    {
        $this->_project = $project;
        return $this;
    }

    /**
     * Run a function
     *
     * @param function $function
     * @return mixed
     * @throws BuildException
     */
    protected function _run($function)
    {
        set_error_handler(function($errno, $errstr) {
            throw new BuildException($errstr);
        });

        try {
            $result = $function();
        } catch (BuildException $e) {
            restore_error_handler();
            throw $e;
        }

        restore_error_handler();

        return $result;
    }

}
