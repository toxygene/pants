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

use InvalidArgumentException;
use Pants\Project;
use Pants\Property\Properties;
use Pants\Target\Target;
use Pants\Target\Targets;
use Pants\Task\Call;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Pants\Target\Call
 */
class CallTest extends TestCase
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
     * @expectedException \Pants\BuildException
     */
    public function testTargetIsRequired()
    {
        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        $this->task
            ->execute($mockProject);
    }

    /**
     * @covers ::getTarget
     * @covers ::setTarget
     */
    public function testTargetIsConfigurable()
    {
        $this->task
            ->setTarget('asdf');

        $this->assertEquals('asdf', $this->task->getTarget());
    }

    /**
     * @covers ::__construct
     * @covers ::execute
     * @expectedException \InvalidArgumentException
     */
    public function testAValidTargetIsRequired()
    {
        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        /** @var Properties|\PHPUnit_Framework_MockObject_MockObject $mockProperties */
        $mockProperties = $this->createMock(Properties::class);

        /** @var Targets|\PHPUnit_Framework_MockObject_MockObject $mockTargets */
        $mockTargets = $this->createMock(Targets::class);

        $mockProject->expects($this->once())
            ->method('getProperties')
            ->will($this->returnValue($mockProperties));

        $mockProperties->expects($this->once())
            ->method('filter')
            ->with('asdf')
            ->will($this->returnValue('asdf'));

        $mockProject->expects($this->once())
            ->method('getTargets')
            ->will($this->returnValue($mockTargets));

        $mockTargets->expects($this->once())
            ->method('__get')
            ->with('asdf')
            ->will($this->throwException(new InvalidArgumentException()));

        $this->task
            ->setTarget('asdf')
            ->execute($mockProject);
    }

    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testRequestedTargetIsExecuted()
    {
        /** @var Target|\PHPUnit_Framework_MockObject_MockObject $mockTarget */
        $mockTarget = $this->createMock(Target::class);

        /** @var Targets|\PHPUnit_Framework_MockObject_MockObject $mockTargets */
        $mockTargets = $this->createMock(Targets::class);

        /** @var Properties|\PHPUnit_Framework_MockObject_MockObject $mockProperties */
        $mockProperties = $this->createMock(Properties::class);

        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        $mockTarget->expects($this->once())
            ->method('execute')
            ->with($mockProject)
            ->will($this->returnSelf());

        $mockTargets->expects($this->once())
            ->method('__get')
            ->with('asdf')
            ->will($this->returnValue($mockTarget));

        $mockProperties->expects($this->once())
            ->method('filter')
            ->with('asdf')
            ->will($this->returnValue('asdf'));

        $mockProject->expects($this->once())
            ->method('getProperties')
            ->will($this->returnValue($mockProperties));

        $mockProject->expects($this->once())
            ->method('getTargets')
            ->will($this->returnValue($mockTargets));

        $this->task
            ->setTarget('asdf')
            ->execute($mockProject);
    }

}
