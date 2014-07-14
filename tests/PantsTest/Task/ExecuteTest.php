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
 */

namespace PantsTest\Task;

use Pants\Task\Execute;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class ExecuteTest extends TestCase
{

    /**
     * Execute task
     * @var Delete
     */
    protected $task;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->task = new Execute($this->getMock('\Pants\Property\Properties'));
    }

    /**
     * Tear down the test
     */
    public function tearDown()
    {
        unset($this->task);
    }

    /**
     * @covers Pants\Task\Execute::execute
     */
    public function testCommandIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->task
            ->execute();
    }

    /**
     * @covers Pants\Task\Execute::execute
     */
    public function testFailureThrowsABuildException()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->task
            ->execute();
    }

    /**
     * @covers Pants\Task\Execute::execute
     */
    public function testExecuteRunsCommand()
    {
        $this->task
            ->setCommand('php success.php')
            ->setDirectory(__DIR__ . '/_files')
            ->execute();
    }
    
    /**
     * @covers Pants\Task\Execute::execute
     */
    public function testFailedCommandThrowsException()
    {
        $this->setExpectedException('Pants\Task\Execute\CommandReturnedErrorException');
        
        $command   = 'php failure.php';
        $directory = __DIR__ . '/_files';

        $this->task
            ->getProperties()
            ->expects($this->at(0))
            ->method('filter')
            ->with($command)
            ->will($this->returnArgument(0));

        $this->task
            ->getProperties()
            ->expects($this->at(1))
            ->method('filter')
            ->with($directory)
            ->will($this->returnArgument(0));

        $this->task
            ->setCommand($command)
            ->setDirectory($directory)
            ->execute();
    }
    
    /**
     * @covers Pants\Task\Execute::getCommand
     * @covers Pants\Task\Execute::setCommand
     */
    public function testCommandCanBeSet()
    {
        $this->task
            ->setCommand('asdf');
            
        $this->assertEquals('asdf', $this->task->getCommand());
    }
    
    /**
     * @covers Pants\Task\Execute::getDirectory
     * @covers Pants\Task\Execute::setDirectory
     */
    public function testDirectoryCanBeSet()
    {
        $this->task
            ->setDirectory('asdf');
            
        $this->assertEquals('asdf', $this->task->getDirectory());
    }

}
