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

namespace PantsTest;

use ArrayIterator;
use Pants\Project;
use Pants\Property\Properties;
use Pants\Target\Target;
use Pants\Target\Targets;
use Pants\Task\Task;
use PHPUnit\Framework\TestCase;

/**
 *
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
        /** @var Task|\PHPUnit_Framework_MockObject_MockObject $mockTask */
        $mockTask = $this->createMock(Task::class);

        $mockTask->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($mockTask));

        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        $this->target
            ->getTasks()
            ->add($mockTask);

        $this->target
            ->execute($mockProject);
    }

    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testTasksAreNotExecutedIfIfIsNotSet()
    {
        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        /** @var Task|\PHPUnit_Framework_MockObject_MockObject $mockTask */
        $mockTask = $this->createMock(Task::class);

        $mockTask->expects($this->never())
            ->method('execute');

        $this->target
            ->getTasks()
            ->add($mockTask);

        $this->target
            ->setIf(array('one'))
            ->execute($mockProject);
    }

    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testTasksAreNotExecutedIfUnlessIsSet()
    {
        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        /** @var Properties|\PHPUnit_Framework_MockObject_MockObject $mockProperties */
        $mockProperties = $this->createMock(Properties::class);

        /** @var Task|\PHPUnit_Framework_MockObject_MockObject $mockTask */
        $mockTask = $this->createMock(Task::class);

        $mockProject->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue($mockProperties));

        $mockProperties->expects($this->once())
            ->method('__get')
            ->with('one')
            ->will($this->returnValue(true));

        $mockTask->expects($this->never())
            ->method('execute');

        $this->target
            ->getTasks()
            ->add($mockTask);

        $this->target
            ->setUnless(array('one'))
            ->execute($mockProject);
    }
    
    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testDependIsExecuted()
    {
        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        /** @var Targets|\PHPUnit_Framework_MockObject_MockObject $mockTargets */
        $mockTargets = $this->createMock(Targets::class);

        /** @var Target|\PHPUnit_Framework_MockObject_MockObject $mockTargets */
        $mockTarget = $this->createMock(Target::class);

        $mockProject->expects($this->any())
            ->method('getTargets')
            ->will($this->returnValue($mockTargets));

        $mockTargets->expects($this->once())
            ->method('__get')
            ->with('test')
            ->will($this->returnValue($mockTarget));

        $mockTarget->expects($this->once())
            ->method('execute')
            ->with($mockProject)
            ->will($this->returnSelf());

        $this->target
            ->setDepends(array('test'))
            ->execute($mockProject);
    }

}
