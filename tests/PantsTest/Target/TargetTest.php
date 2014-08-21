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

use ArrayIterator;
use Pants\Target\Target;
use Pants\Task\Tasks;
use PHPUnit_Framework_TestCase as TestCase;

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
     * Properties mock object
     *
     * @var \Pants\Property\Properties
     */
    protected $properties;

    /**
     * Targets mock object
     *
     * @var \Pants\Target\Targets
     */
    protected $targets;

    /**
     * Tasks mock object
     *
     * @var \Pants\Task\Tasks
     */
    protected $tasks;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->targets    = $this->getMock('\Pants\Target\Targets');
        $this->properties = $this->getMock('\Pants\Property\Properties');
        $this->tasks      = $this->getMock('\Pants\Task\Tasks');
        
        $this->target = new Target($this->targets, $this->properties, $this->tasks);
    }

    /**
     * @covers Pants\Target\Target::getDepends
     * @covers Pants\Target\Target::setDepends
     */
    public function testDependsCanBeSet()
    {
        $this->target->setDepends(array('depends'));

        $this->assertEquals(array('depends'), $this->target->getDepends());
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
     * @covers Pants\Target\Target::getHidden
     * @covers Pants\Target\Target::setHidden
     */
    public function testHiddenCanBeSet()
    {
        $this->target->setHidden(true);

        $this->assertTrue($this->target->getHidden());
    }

    /**
     * @covers Pants\Target\Target::getIf
     * @covers Pants\Target\Target::setIf
     */
    public function testIfCanBeSet()
    {
        $this->target->setIf(array('if'));

        $this->assertEquals(array('if'), $this->target->getIf());
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
     * @covers Pants\Target\Target::getUnless
     * @covers Pants\Target\Target::setUnless
     */
    public function testUnlessCanBeSet()
    {
        $this->target->setUnless(array('unless'));

        $this->assertEquals(array('unless'), $this->target->getUnless());
    }

    /**
     * @covers Pants\Target\Target::__construct
     * @covers Pants\Target\Target::execute
     */
    public function testTasksAreExecutedOnTargetExecute()
    {
        $task = $this->getMock('\Pants\Task\Task');

        $task->expects($this->exactly(2))
            ->method('execute')
            ->will($this->returnValue($task));
            
        $this->tasks
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator(array($task, $task))));

        $this->target
            ->execute();
    }

    /**
     * @covers Pants\Target\Target::__construct
     * @covers Pants\Target\Target::execute
     */
    public function testTasksAreNotExecutedIfIfIsNotSet()
    {
        $task = $this->getMock('\Pants\Task\Task');

        $task->expects($this->never())
            ->method('execute');

        $this->tasks
            ->add($task);

        $this->target
            ->setIf(array('one'))
            ->execute();
    }

    /**
     * @covers Pants\Target\Target::__construct
     * @covers Pants\Target\Target::execute
     */
    public function testTasksAreNotExecutedIfUnlessIsSet()
    {
        $task = $this->getMock('\Pants\Task\Task');

        $task->expects($this->never())
            ->method('execute');

        $this->tasks
            ->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator(array($task))));

        $this->properties
            ->expects($this->once())
            ->method('__get')
            ->with('one')
            ->will($this->returnValue(true));

        $this->target
            ->setUnless(array('one'))
            ->execute();
    }
    
    /**
     * @covers Pants\Target\Target::__construct
     * @covers Pants\Target\Target::execute
     */
    public function testDependIsExecuted()
    {
        $target = $this->getMockBuilder('\Pants\Target\Target')
            ->disableOriginalConstructor()
            ->getMock();

        $target->expects($this->once())
            ->method('execute')
            ->will($this->returnSelf());

        $this->targets
            ->expects($this->once())
            ->method('__get')
            ->with('test')
            ->will($this->returnValue($target));

        $this->tasks
            ->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator()));

        $this->target
            ->setDepends(array('test'))
            ->execute();
    }

}
