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

use Pants\Project;
use Pants\Property\Properties;
use Pants\Task\Property;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Pants\Task\Property
 */
class PropertyTest extends TestCase
{

    /**
     * Properties task
     * @var Property
     */
    protected $task;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setup();

        $this->task = new Property();
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
     * @covers ::getName
     * @covers ::setName
     */
    public function testNameCanBeSet()
    {
        $this->task
            ->setName('one');

        $this->assertEquals('one', $this->task->getName());
    }

    /**
     * @covers ::getValue
     * @covers ::setValue
     */
    public function testValueCanBeSet()
    {
        $this->task
            ->setValue('one');

        $this->assertEquals('one', $this->task->getValue());
    }

    /**
     * @covers ::execute
     * @expectedException \Pants\BuildException
     */
    public function testNameIsRequired()
    {
        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        $this->task
            ->setValue('value')
            ->execute($mockProject);
    }

    /**
     * @covers ::execute
     */
    public function testPropertiesAreSetOnTheProjectOnExecute()
    {
        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        /** @var Properties|\PHPUnit_Framework_MockObject_MockObject $mockProperties */
        $mockProperties = $this->createMock(Properties::class);

        $mockProject->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue($mockProperties));

        $mockProperties->expects($this->any())
            ->method('filter')
            ->with($this->anything())
            ->will($this->returnArgument(0));

        $mockProperties->expects($this->once())
            ->method('__set')
            ->with('one', 'two');
            
        $this->task
            ->setName('one')
            ->setValue('two')
            ->execute($mockProject);
    }

}
