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

namespace PantsTest;

use Pants\ContextInterface;
use Pants\Project;
use Pants\Property\Properties;
use Pants\Property\PropertiesInterface;
use Pants\Target\Target;
use Pants\Target\Targets;
use Pants\Task\TaskInterface;
use Pants\Task\Tasks;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Pants\Project
 */
class ProjectTest extends TestCase
{

    /**
     * Project
     * @var Project
     */
    protected $project;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        parent::setUp();

        $this->project = new Project(
            new Properties(),
            new Targets(),
            new Tasks()
        );
    }

    /**
     * Tear down the test case
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->project);
    }

    /**
     * @covers ::execute
     */
    public function testTasksAreExecutedBeforeTargets()
    {
        /** @var TaskInterface|\PHPUnit_Framework_MockObject_MockObject $task */
        $task = $this->createMock(TaskInterface::class);

        $task->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(ContextInterface::class))
            ->will($this->returnSelf());

        $this->project
            ->getTasks()
            ->add($task);

        $this->project
            ->execute();
    }

    /**
     * @covers ::execute
     */
    public function testDefaultTargetIsExecutedIfNoTargetsAreSpecified()
    {
        /** @var Target|\PHPUnit_Framework_MockObject_MockObject $target */
        $target = $this->createMock(Target::class);

        $target->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('default'));

        $target->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(ContextInterface::class))
            ->will($this->returnSelf());

        $this->project
            ->getProperties()
            ->add(PropertiesInterface::DEFAULT_TARGET_NAME, 'default');

        $this->project
            ->getTargets()
            ->add($target);

        $this->project
            ->execute();
    }

}
