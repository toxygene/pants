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
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class AbstractTaskTest extends TestCase
{

    /**
     * Task
     *
     * @var Pants\Task\AbstractTask
     */
    protected $task;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->task = $this->getMockForAbstractClass('Pants\Task\AbstractTask');
    }
    
    /**
     * Tear down the test case
     */
    public function tearDown()
    {
        unset($this->task);
    }
    
    /**
     * @covers Pants\Task\AbstractTask::getProject
     * @covers Pants\Task\AbstractTask::setProject
     */
    public function testProjectCanBeSet()
    {
        $project = new Project();
        $this->task->setProject($project);
        $this->assertSame($project, $this->task->getProject());
    }
    
    /**
     * @covers Pants\Task\AbstractTask::setOptions
     */
    public function testBadMethodCallExceptionIsThrownOnInvalidOption()
    {
        $this->setExpectedException('BadMethodCallException');
        
        $this->task->setOptions(array('invalid' => 'invalid'));
    }
    
    /**
     * @covers Pants\Task\AbstractTask::setOptions
     */
    public function testOptionsCanBeSet()
    {
        $project = new Project();
        $this->task->setOptions(array('project' => $project));
        $this->assertSame($project, $this->task->getProject());
    }
    
    /**
     * @covers Pants\Task\AbstractTask::filterProperties
     */
    public function testBuildExceptionIsThrownIfNoProjectIsSetWhenFilteringProperties()
    {
        $this->setExpectedException('Pants\BuildException');
        
        $this->task->filterProperties('@asdf@');
    }
    
    /**
     * @covers Pants\Task\AbstractTask::filterProperties
     */
    public function testFilterPropertiesUsesThePropertiesObjectToFilter()
    {
        $properties = $this->getMock('Pants\Property\Properties');
        $properties->expects($this->once())
            ->method('filter')
            ->with('@test@')
            ->will($this->returnValue('test'));

        $project = $this->getMock('Pants\Project');
        $project->expects($this->once())
            ->method('getProperties')
            ->will($this->returnValue($properties));
            
        $this->task
            ->setProject($project);

        $this->assertEquals('test', $this->task->filterProperties('@test@'));
    }

}
