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
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
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
        $this->project = new Project();
    }
    
    /**
     * Tear down the test case
     */
    public function tearDown()
    {
        unset($this->project);
    }

    /**
     * @covers Pants\Project::getDefault
     * @covers Pants\Project::setDefault
     */
    public function testDefaultCanBeSet()
    {
        $this->project->setDefault('test');

        $this->assertEquals('test', $this->project->getDefault());
    }

    /**
     * @covers Pants\Project::getProperties
     */
    public function testPropertiesCanBeRetrieved()
    {
        $this->assertInstanceOf('Pants\Property\Properties', $this->project->getProperties());
    }

    /**
     * @covers Pants\Project::execute
     */
    public function testTasksAreExecutedBeforeTargets()
    {
        $task = $this->getMock('\Pants\Task\AbstractTask');

        $task->expects($this->once())
            ->method('setProject')
            ->with($this->project)
            ->will($this->returnValue($task));

        $task->expects($this->once())
            ->method('execute')
            ->will($this->returnValue($task));

        $this->project
            ->getTasks()
            ->add($task);

        $this->project
            ->execute();
    }

    /**
     * @covers Pants\Project::execute
     */
    public function testDefaultTargetIsExecutedIfNoTargetsAreSpecified()
    {
        $target = $this->getMock('\Pants\Target\Target');

        $target->expects($this->once())
              ->method('getName')
              ->will($this->returnValue('default'));

        $target->expects($this->once())
              ->method('setProject')
              ->with($this->project)
              ->will($this->returnValue($target));

        $target->expects($this->once())
              ->method('execute')
              ->will($this->returnValue($target));

        $this->project
            ->setDefault('default')
            ->getTargets()
            ->add($target);

        $this->project
            ->execute();
    }

    /**
     * @covers Pants\Project::execute
     */
    public function testBaseDirChangesTheCurrentWorkingDirectory()
    {
        $cwd = getcwd();

        $this->project
            ->setBaseDir('/')
            ->execute();

        $this->assertEquals('/', getcwd());

        chdir($cwd);
    }

}
