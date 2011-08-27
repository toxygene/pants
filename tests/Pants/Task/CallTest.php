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
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
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

use Pants\Project,
    Pants\Task\Call,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class CallTest extends TestCase
{

    /**
     * Call task
     * @var Call
     */
    protected $_call;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->_call = new Call();
        $this->_call->setProject(new Project());
    }

    public function testTargetCanBeSet()
    {
        $this->_call->setTarget("asdf");

        $this->assertEquals("asdf", $this->_call->getTarget());
    }

    public function testCallingAnInvalidTargetThrowsAnInvalidArgumentException()
    {
        $this->setExpectedException("\InvalidArgumentException");

        $this->_call
             ->setTarget("asdf")
             ->execute();
    }

    public function testCallingAValidTargetExecutesTheTarget()
    {
        $project = new Project();

        $mock = $this->getMock("Pants\Target");

        $mock->expects($this->any())
             ->method("getName")
             ->will($this->returnValue("asdf"));

        $mock->expects($this->once())
             ->method("setProject")
             ->with($project)
             ->will($this->returnValue($mock));

        $mock->expects($this->once())
             ->method("execute")
             ->will($this->returnValue($mock));

        $project->getTargets()
                ->add($mock);

        $this->_call
             ->setProject($project)
             ->setTarget("asdf")
             ->execute();
    }

}
