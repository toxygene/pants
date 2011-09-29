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

use Pants\BuildException;

/**
 * Execute a shell command task
 *
 * @package Pants
 * @subpackage Task
 */
class Execute extends AbstractTask
{

    /**
     * Command to execute
     * @var string
     */
    protected $_command;

    /**
     * Directory to execute the command in
     * @var string
     */
    protected $_directory;

    /**
     * Execute the task
     *
     * @return Exec
     * @throws BuildException
     */
    public function execute()
    {
        if (!$this->getCommand()) {
            throw new BuildException("Command is not set");
        }

        $command   = $this->filterProperties($this->getCommand());
        $directory = $this->filterProperties($this->getDirectory());

        $this->_runInDirectory($this->getDirectory(), function() use ($command) {
            exec($command);
        });

        return $this;
    }

    /**
     * Change the working directory
     *
     * @param string $directory
     * @return boolean
     */
    protected function _chdir($directory)
    {
        if ($directory) {
            return $this->_run(function() use ($directory) {
                return chdir($directory);
            });
        }
    }

    /**
     * Change the working directory and run the function
     *
     * @param string $directory
     * @param function $function
     */
    protected function _runInDirectory($directory, $function)
    {
        $cwd = getcwd();

        $this->_chdir($directory);

        try {
            $return = $this->_run($function);
            $this->_chdir($cwd);
        } catch (Exception $e) {
            $this->_chdir($cwd);
            throw $e;
        }

        return $return;
    }

    /**
     * Get the command to execute
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->_command;
    }

    /**
     * Get the directory to execute the command in
     *
     * @param string
     */
    public function getDirectory()
    {
        return $this->_directory;
    }

    /**
     * Set the command to execute
     *
     * @param string $command
     * @return Execute
     */
    public function setCommand($command)
    {
        $this->_command = $command;
        return $this;
    }

    /**
     * Set the directory to execute the command in
     *
     * @param string $directory
     * @return Execute
     */
    public function setDirectory($directory)
    {
        $this->_directory = $directory;
        return $this;
    }

}
