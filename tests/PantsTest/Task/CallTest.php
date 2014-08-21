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

use InvalidArgumentException;
use Pants\Task\Call;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class CallTest extends TestCase
{

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
     * Call task
     *
     * @var Call
     */
    protected $task;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->properties = $this->getMock('\Pants\Property\Properties');
        
        $this->targets = $this->getMockBuilder('\Pants\Target\Targets')
            ->disableOriginalConstructor()
            ->getMock();
    
        $this->task = new Call(
            $this->properties,
            $this->targets
        );
    }
    
    /**
     * Tear down the test case
     */
    public function tearDown()
    {
        unset($this->properties);
        unset($this->targets);
        unset($this->task);
    }

    /**
     * @covers Pants\Task\Call::execute
     */
    public function testTargetIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->task
            ->execute();
    }

    /**
     * @covers Pants\Task\Call::getTarget
     * @covers Pants\Task\Call::setTarget
     */
    public function testTargetIsConfigurable()
    {
        $this->task
            ->setTarget('asdf');

        $this->assertEquals('asdf', $this->task->getTarget());
    }

    /**
     * @covers Pants\Task\Call::__construct
     * @covers Pants\Task\Call::execute
     */
    public function testAValidTargetIsRequired()
    {
        $this->setExpectedException('\InvalidArgumentException');
        
        $this->properties
            ->expects($this->once())
            ->method('filter')
            ->with('asdf')
            ->will($this->returnArgument(0));

        $this->targets
            ->expects($this->once())
            ->method('__get')
            ->will($this->throwException(new InvalidArgumentException()));

        $this->task
            ->setTarget('asdf')
            ->execute();
    }

    /**
     * @covers Pants\Task\Call::__construct
     * @covers Pants\Task\Call::execute
     */
    public function testRequestedTargetIsExecuted()
    {
        $this->properties
            ->expects($this->once())
            ->method('filter')
            ->with('asdf')
            ->will($this->returnArgument(0));
            
        $target = $this->getMockBuilder('\Pants\Target\Target')
            ->disableOriginalConstructor()
            ->getMock();
        
        $target->expects($this->once())
            ->method('execute')
            ->will($this->returnSelf());

        $this->targets
            ->expects($this->once())
            ->method('__get')
            ->with('asdf')
            ->will($this->returnValue($target));

        $this->task
            ->setTarget('asdf')
            ->execute();
    }

}
