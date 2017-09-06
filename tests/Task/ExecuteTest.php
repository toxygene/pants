<?php
/**
 * Pants
 *
 * Copyright (c) 2011-2017, Justin Hendrickson
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

use Pants\Project;
use Pants\Property\Properties;
use Pants\Task\Execute;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Pants\Task\Execute
 */
class ExecuteTest extends TestCase
{

    /**
     * Execute task
     * @var Execute
     */
    protected $task;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->task = new Execute();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->task);
    }

    /**
     * @covers ::execute
     * @expectedException \Pants\BuildException
     */
    public function testCommandIsRequired()
    {
        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        $this->task
            ->execute($mockProject);
    }

    /**
     * @covers ::execute
     * @expectedException \Pants\BuildException
     */
    public function testFailureThrowsABuildException()
    {
        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        $this->task
            ->execute($mockProject);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteRunsCommand()
    {
        $directory = __DIR__ . '/_files';
        $command = 'exit'; // todo does this work on windows?

        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        /** @var Properties|\PHPUnit_Framework_MockObject_MockObject $mockProperties */
        $mockProperties = $this->createMock(Properties::class);

        $mockProject->expects($this->exactly(2))
            ->method('getProperties')
            ->will($this->returnValue($mockProperties));

        $mockProperties->expects($this->at(0))
            ->method('filter')
            ->with($command)
            ->will($this->returnArgument(0));

        $mockProperties->expects($this->at(1))
            ->method('filter')
            ->with($directory)
            ->will($this->returnArgument(0));

        $this->task
            ->setCommand($command)
            ->setDirectory($directory)
            ->execute($mockProject);
    }
    
    /**
     * @covers ::__construct
     * @covers ::execute
     * @expectedException \Pants\Task\Execute\CommandReturnedErrorException
     */
    public function testFailedCommandThrowsException()
    {
        $directory = __DIR__ . '/_files';
        $command = 'exit 1'; // todo does this work on windows?

        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        /** @var Properties|\PHPUnit_Framework_MockObject_MockObject $mockProperties */
        $mockProperties = $this->createMock(Properties::class);

        $mockProject->expects($this->exactly(2))
            ->method('getProperties')
            ->will($this->returnValue($mockProperties));

        $mockProperties->expects($this->at(0))
            ->method('filter')
            ->with($command)
            ->will($this->returnArgument(0));

        $mockProperties->expects($this->at(1))
            ->method('filter')
            ->with($directory)
            ->will($this->returnArgument(0));

        $this->task
            ->setCommand($command)
            ->setDirectory($directory)
            ->execute($mockProject);
    }
    
    /**
     * @covers ::getCommand
     * @covers ::setCommand
     */
    public function testCommandCanBeSet()
    {
        $this->task
            ->setCommand('asdf');
            
        $this->assertEquals('asdf', $this->task->getCommand());
    }
    
    /**
     * @covers ::getDirectory
     * @covers ::setDirectory
     */
    public function testDirectoryCanBeSet()
    {
        $this->task
            ->setDirectory('asdf');
            
        $this->assertEquals('asdf', $this->task->getDirectory());
    }

}
