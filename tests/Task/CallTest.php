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

use Pants\Target\Executor\ExecutorInterface;
use Pants\Task\Exception\TaskException;
use Pants\Task\Call;
use Pants\Task\TaskInterface;

/**
 * @coversDefaultClass \Pants\Task\Call
 */
class CallTest extends TaskTestCase
{

    /**
     * Call task
     *
     * @var Call
     */
    protected $task;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->task = new Call();
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
    public function testExecutingTheTaskWithoutATargetCausesABuildException()
    {
        $this->task
            ->execute($this->mockContext);
    }

    /**
     * @covers ::getTarget
     * @covers ::setTarget
     */
    public function testTheTargetCantBeInspectedAndChanged()
    {
        $this->task
            ->setTarget('asdf');

        $this->assertEquals('asdf', $this->task->getTarget());
    }

    /**
     * @covers ::execute
     * @expectedException \Pants\Task\Exception\TaskException
     */
    public function testExecutingTheTaskWithAnInvalidTargetCausesABuildException()
    {
        /** @var ExecutorInterface|\PHPUnit_Framework_MockObject_MockObject $mockExecutor */
        $mockExecutor = $this->createMock(ExecutorInterface::class);

        /** @var TaskInterface|\PHPUnit_Framework_MockObject_MockObject $mockTask */
        $mockTask = $this->createMock(TaskInterface::class);

        $this->mockContext
            ->expects($this->once())
            ->method('getExecutor')
            ->will($this->returnValue($mockExecutor));

        $mockExecutor->expects($this->once())
            ->method('executeSingle')
            ->with('asdf', $this->mockContext)
            ->will($this->throwException(new TaskException('message', $this->mockCurrentTarget, $mockTask)));

        $this->task
            ->setTarget('asdf')
            ->execute($this->mockContext);
    }

    /**
     * @covers ::execute
     */
    public function testExecutingTheTaskCausesTheTargetToBeExecuted()
    {
        /** @var ExecutorInterface|\PHPUnit_Framework_MockObject_MockObject */
        $mockExecutor = $this->createMock(ExecutorInterface::class);

        $this->mockContext
            ->expects($this->once())
            ->method('getExecutor')
            ->will($this->returnValue($mockExecutor));

        $mockExecutor->expects($this->once())
            ->method('executeSingle')
            ->with('asdf', $this->mockContext)
            ->will($this->returnSelf());

        $this->task
            ->setTarget('asdf')
            ->execute($this->mockContext);
    }

}
