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

use Pants\Project;
use Pants\Target\Target;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class TargetTest extends TestCase
{

    /**
     * Project
     *
     * @var Project
     */
    protected $project;

    /**
     * Target
     *
     * @var Target
     */
    protected $target;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->target = new Target();
        $this->project = new Project();

        $this->target->setProject($this->project);
    }

    /**
     * @covers Pants\Target\Target::getName
     * @covers Pants\Target\Target::setName
     */
    public function testNameCanBeSet()
    {
        $this->target->setName('test');

        $this->assertEquals('test', $this->target->getName());
    }

    /**
     * @covers Pants\Target\Target::getDescription
     * @covers Pants\Target\Target::setDescription
     */
    public function testDescriptionCanBeSet()
    {
        $this->target->setDescription('test');

        $this->assertEquals('test', $this->target->getDescription());
    }

    /**
     * @covers Pants\Target\Target::getTasks
     */
    public function testTasksCanBeRetrieved()
    {
        $this->assertInstanceOf('\Pants\Task\Tasks', $this->target->getTasks());
    }

    /**
     * @covers Pants\Target\Target::execute
     */
    public function testTasksAreExecutedOnTargetExecute()
    {
        $task = $this->getMock('\Pants\Task\Task');

        $task->expects($this->exactly(2))
             ->method('setProject')
             ->with($this->project)
             ->will($this->returnValue($task));

        $task->expects($this->exactly(2))
             ->method('execute')
             ->will($this->returnValue($task));

        $this->target
             ->getTasks()
             ->add($task)
             ->add($task);

        $this->target
             ->execute();
    }

    /**
     * @covers Pants\Target\Target::execute
     */
    public function testTasksAreNotExecutedIfIfIsNotSet()
    {
        $task = $this->getMock('\Pants\Task\Task');

        $task->expects($this->never())
             ->method('execute');

        $this->target
             ->getTasks()
             ->add($task);

        $this->target
             ->setIf(array('one'))
             ->execute();
    }

    /**
     * @covers Pants\Target\Target::execute
     */
    public function testTasksAreNotExecutedIfUnlessIsSet()
    {
        $task = $this->getMock('\Pants\Task\Task');

        $task->expects($this->never())
             ->method('execute');

        $this->target
             ->getTasks()
             ->add($task);

        $this->target
             ->getProject()
             ->getProperties()
             ->one = true;

        $this->target
             ->setUnless(array('one'))
             ->execute();
    }

}
