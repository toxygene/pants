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

namespace Pants\Test\Target;

use Pants\ContextInterface;
use Pants\Property\Properties;
use Pants\Target\Executor\ExecutorInterface;
use Pants\Target\Target;
use Pants\Task\TaskInterface;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Pants\Target\Target
 */
class TargetTest extends TestCase
{

    /**
     * Target
     *
     * @var Target
     */
    protected $target;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->target = new Target('target');
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->target);
    }


    /**
     * @covers ::getDepends
     * @covers ::setDepends
     */
    public function testDependsCanBeSet()
    {
        $this->target->setDepends(array('depends'));

        $this->assertEquals(array('depends'), $this->target->getDepends());
    }

    /**
     * @covers ::getDescription
     * @covers ::setDescription
     */
    public function testDescriptionCanBeSet()
    {
        $this->target->setDescription('test');

        $this->assertEquals('test', $this->target->getDescription());
    }

    /**
     * @covers ::getHidden
     * @covers ::setHidden
     */
    public function testHiddenCanBeSet()
    {
        $this->target->setHidden(true);

        $this->assertTrue($this->target->getHidden());
    }

    /**
     * @covers ::getIf
     * @covers ::setIf
     */
    public function testIfCanBeSet()
    {
        $this->target->setIf(array('if'));

        $this->assertEquals(array('if'), $this->target->getIf());
    }

    /**
     * @covers ::getUnless
     * @covers ::setUnless
     */
    public function testUnlessCanBeSet()
    {
        $this->target->setUnless(array('unless'));

        $this->assertEquals(array('unless'), $this->target->getUnless());
    }

    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testTasksAreExecutedOnTargetExecute()
    {
        /** @var TaskInterface|\PHPUnit_Framework_MockObject_MockObject $mockTask */
        $mockTask = $this->createMock(TaskInterface::class);

        $mockTask->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($mockTask));

        /** @var ContextInterface|\PHPUnit_Framework_MockObject_MockObject $mockContext */
        $mockContext = $this->createMock(ContextInterface::class);

        $this->target
            ->getTasks()
            ->add($mockTask);

        $this->target
            ->execute($mockContext);
    }

    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testTasksAreNotExecutedIfIfIsNotSet()
    {
        /** @var ContextInterface|\PHPUnit_Framework_MockObject_MockObject $mockContext */
        $mockContext = $this->createMock(ContextInterface::class);

        /** @var TaskInterface|\PHPUnit_Framework_MockObject_MockObject $mockTask */
        $mockTask = $this->createMock(TaskInterface::class);

        $mockTask->expects($this->never())
            ->method('execute');

        $this->target
            ->getTasks()
            ->add($mockTask);

        $this->target
            ->setIf(array('one'))
            ->execute($mockContext);
    }

    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testTasksAreNotExecutedIfUnlessIsSet()
    {
        /** @var ContextInterface|\PHPUnit_Framework_MockObject_MockObject $mockContext */
        $mockContext = $this->createMock(ContextInterface::class);

        /** @var Properties|\PHPUnit_Framework_MockObject_MockObject $mockProperties */
        $mockProperties = $this->createMock(Properties::class);

        /** @var TaskInterface|\PHPUnit_Framework_MockObject_MockObject $mockTask */
        $mockTask = $this->createMock(TaskInterface::class);

        $mockContext->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue($mockProperties));

        $mockProperties->expects($this->any())
            ->method('exists')
            ->with('one')
            ->will($this->returnValue(true));

        $mockProperties->expects($this->any())
            ->method('get')
            ->with('one')
            ->will($this->returnValue(true));

        $mockTask->expects($this->never())
            ->method('execute')
            ->with($mockContext)
            ->will($this->returnSelf());

        $this->target
            ->getTasks()
            ->add($mockTask);

        $this->target
            ->setUnless(array('one'))
            ->execute($mockContext);
    }
    
    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testDependIsExecuted()
    {
        /** @var ContextInterface|\PHPUnit_Framework_MockObject_MockObject $mockContext */
        $mockContext = $this->createMock(ContextInterface::class);

        /** @var ExecutorInterface|\PHPUnit_Framework_MockObject_MockObject $mockExecutor */
        $mockExecutor = $this->createMock(ExecutorInterface::class);

        $mockContext->expects($this->any())
            ->method('getExecutor')
            ->will($this->returnValue($mockExecutor));

        $mockExecutor->expects($this->once())
            ->method('executeSingle')
            ->with('test')
            ->will($this->returnSelf());

        $this->target
            ->setDepends(array('test'))
            ->execute($mockContext);
    }

}
