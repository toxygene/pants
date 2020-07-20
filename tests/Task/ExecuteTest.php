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

namespace Pants\Test\Task;

use Pants\Task\Execute;

/**
 * @coversDefaultClass \Pants\Task\Execute
 */
class ExecuteTest extends TaskTestCase
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
     * @expectedException \Pants\Task\Exception\TaskException
     */
    public function testCommandIsRequired()
    {
        $this->task
            ->execute($this->mockContext);
    }

    /**
     * @covers ::execute
     * @expectedException \Pants\Task\Exception\TaskException
     */
    public function testFailureThrowsABuildException()
    {
        $this->task
            ->setCommand('asdf')
            ->setPrintStderr(false)
            ->execute($this->mockContext);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteRunsCommand()
    {
        $this->markTestIncomplete('Implement storing stdout to property');

        // todo store output

        $directory = __DIR__ . '/_files';
        $command = 'echo "test"'; // todo does this work on windows?

        $this->mockProperties
            ->expects($this->once())
            ->method('add')
            ->with('stdout', 'test');

        $this->task
            ->setCommand($command)
            ->setDirectory($directory)
            ->setStdoutPropertyName('stdout')
            ->execute($this->mockContext);
    }
    
    /**
     * @covers ::__construct
     * @covers ::execute
     * @expectedException \Pants\Task\Exception\TaskException
     */
    public function testFailedCommandThrowsException()
    {
        $this->markTestIncomplete('Implement storing stderr and return value to property');

        // todo store stderr
        // todo store return value

        $directory = __DIR__ . '/_files';
        $command = 'exit 1'; // todo does this work on windows?

        $this->mockProperties
            ->expects($this->once())
            ->method('add')
            ->with('stderr', 'error');

        $this->mockProperties
            ->expects($this->once())
            ->method('add')
            ->with('return value', 1);

        $this->task
            ->setCommand($command)
            ->setDirectory($directory)
            ->setStderrPropertyName('stderr')
            ->setReturnValuePropertyName('return value')
            ->execute($this->mockContext);
    }
}
