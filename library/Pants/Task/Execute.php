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

namespace Pants\Task;

use Pale\Pale;
use Pants\BuildException;
use Pants\Property\Properties;
use Pants\Task\Execute\CommandReturnedErrorException;

/**
 * Execute a shell command task
 *
 * @package Pants\Task
 */
class Execute implements Task
{

    /**
     * Command to execute
     *
     * @var string
     */
    protected $command;

    /**
     * Directory to execute the command in
     *
     * @var string
     */
    protected $directory;

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
     * @return Exec
     * @throws BuildException
     */
    public function execute()
    {
        if (!$this->getCommand()) {
            throw new BuildException('Command is not set');
        }

        $command   = $this->properties->filter($this->getCommand());
        $directory = $this->properties->filter($this->getDirectory());

        $result = Pale::run(function() use ($command, $directory) {
            $descriptorSpec = array(
                0 => array('pipe', 'r'),
                1 => array('pipe', 'w'),
                2 => array('pipe', 'w')
            );
            
            $process = proc_open(
                $command,
                $descriptorSpec,
                $pipes,
                $directory
            );
            
            if (!$process) {
                throw new CommandFailedException($command, $directory);
            }
            
            fclose($pipes[0]);
            
            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            
            $return = proc_close($process);
            
            return array(
                'stdout' => $stdout,
                'stderr' => $stderr,
                'return' => $return
            );
        });

        if ($result['return']) {
            throw new CommandReturnedErrorException(
                $command,
                $directory,
                $result['return'],
                $result['stdout'],
                $result['stderr']
            );
        }

        return $this;
    }

    /**
     * Get the command to execute
     *
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Get the directory to execute the command in
     *
     * @param string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Set the command to execute
     *
     * @param string $command
     * @return Execute
     */
    public function setCommand($command)
    {
        $this->command = $command;
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
        $this->directory = $directory;
        return $this;
    }

}
