<?php
/**
 * Pants
 *
 * Copyright (c) 2011-2018, Justin Hendrickson
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

use JMS\Serializer\Annotation as JMS;
use Pants\ContextInterface;

/**
 * Execute a shell command task
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Task
 */
class Execute implements TaskInterface
{

    /**
     * Command to execute
     *
     * @JMS\Expose()
     * @JMS\SerializedName("command")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
     */
    protected $command;

    /**
     * Directory to execute the command in
     *
     * @JMS\Expose()
     * @JMS\SerializedName("directory")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
     */
    protected $directory;

    /**
     * Flag to print standard error on failure
     *
     * @JMS\Expose()
     * @JMS\SerializedName("print-stderr")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("boolean")
     * @JMS\XmlElement(cdata=false)
     *
     * @var bool|null
     */
    protected $printStderr;

    /**
     * Flag to print standard output on success
     *
     * @JMS\Expose()
     * @JMS\SerializedName("print-stdout")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("boolean")
     * @JMS\XmlElement(cdata=false)
     *
     * @var bool|null
     */
    protected $printStdout;

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        // todo add support for storing stdout to a property
        // todo add support for storing stderr to a property
        // todo add support for command exit code to a property

        $command = $context->getProperties()
            ->filter($this->command);

        $directory = $context->getProperties()
            ->filter($this->directory);

        if (null === $command) {
            $message = 'Command not set';

            $context->getLogger()->error(
                $message,
                [
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            throw new BuildException(
                $message,
                $context->getCurrentTarget(),
                $this
            );
        }

        $descriptorSpec = array(
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w')
        );

        $context->getLogger()->debug(
            sprintf(
                'Executing command "%s" in directory "%s"',
                $command,
                $directory
            ),
            [
                'target' => $context->getCurrentTarget()
                    ->getName()
            ]
        );

        $process = proc_open(
            $command,
            $descriptorSpec,
            $pipes,
            $directory
        );

        if (false === $process) {
            $message = sprintf(
                'Could not execute command "%s" in directory "%s"',
                $command,
                $directory
            );

            $context->getLogger()->error(
                $message,
                [
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            throw new BuildException(
                $message,
                $context->getCurrentTarget(),
                $this
            );
        }

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $return = proc_close($process);

        if (0 !== $return) {
            $printStderr = $this->printStderr ?? true;

            if ($printStderr) {
                echo $stderr;
            }

            $message = sprintf(
                'Command "%s" in directory "%s" failed because "%s"',
                $command,
                $directory,
                $stderr
            );

            $context->getLogger()->error(
                $message,
                [
                    'target' => $context->getCurrentTarget()
                ]
            );

            throw new BuildException(
                $message,
                $context->getCurrentTarget(),
                $this
            );
        }

        $printStdout = $this->printStdout ?? true;

        if ($printStdout) {
            echo $stdout;
        }

        return $this;
    }

    /**
     * Set the print standard error on failure flag
     *
     * @param boolean $printStderr
     * @return self
     */
    public function setPrintStderr(bool $printStderr): self
    {
        $this->printStderr = $printStderr;
        return $this;
    }

    /**
     * Set the print standard output on success flag
     *
     * @param boolean $printStdout
     * @return self
     */
    public function setPrintStdout(bool $printStdout): self
    {
        $this->printStdout = $printStdout;
        return $this;
    }

    /**
     * Set the command to execute
     *
     * @param string $command
     * @return self
     */
    public function setCommand(string $command): self
    {
        $this->command = $command;
        return $this;
    }

    /**
     * Set the directory to execute the command in
     *
     * @param string $directory
     * @return self
     */
    public function setDirectory(string $directory): self
    {
        $this->directory = $directory;
        return $this;
    }
}
