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

use Pants\Project;
use Pants\Task\Call;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class CallTest extends TestCase
{

    /**
     * Call task
     * @var Call
     */
    protected $call;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->call = new Call();

        $this->call
            ->setProject(new Project());
    }
    
    /**
     * Tear down the test case
     */
    public function tearDown()
    {
        unset($this->call);
    }

    /**
     * @covers Pants\Task\Call::execute
     */
    public function testTargetIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->call
            ->execute();
    }

    /**
     * @covers Pants\Task\Call::getTarget
     * @covers Pants\Task\Call::setTarget
     */
    public function testTargetIsConfigurable()
    {
        $this->call
            ->setTarget('asdf');

        $this->assertEquals('asdf', $this->call->getTarget());
    }

    /**
     * @covers Pants\Task\Call::execute
     */
    public function testAValidTargetIsRequired()
    {
        $this->setExpectedException('\InvalidArgumentException');

        $this->call
            ->setTarget('asdf')
            ->execute();
    }

    /**
     * @covers Pants\Task\Call::execute
     */
    public function testRequestedTargetIsExecuted()
    {
        $project = new Project();

        // Setup the mock target that will be called
        $mock = $this->getMock('Pants\Target\Target');

        $mock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('asdf'));

        $mock->expects($this->once())
            ->method('setProject')
            ->with($project)
            ->will($this->returnValue($mock));

        $mock->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($mock));

        // Add the mock target to the project
        $project->getTargets()
            ->add($mock);

        // Call the mock target in the project
        $this->call
            ->setProject($project)
            ->setTarget('asdf')
            ->execute();
    }

}