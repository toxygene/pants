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
use Pants\Task\Property;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class PropertyTest extends TestCase
{

    /**
     * Properties task
     * @var Property
     */
    protected $task;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->task = new Property();
        $this->task->setProject(new Project());
    }
    
    /**
     * Tear down the test case
     */
    public function tearDown()
    {
        unset($this->task);
    }

    /**
     * @covers Pants\Task\Property::getName
     * @covers Pants\Task\Property::setName
     */
    public function testNameCanBeSet()
    {
        $this->task->setName('one');
        $this->assertEquals('one', $this->task->getName());
    }

    /**
     * @covers Pants\Task\Property::getValue
     * @covers Pants\Task\Property::setValue
     */
    public function testValueCanBeSet()
    {
        $this->task->setValue('one');
        $this->assertEquals('one', $this->task->getValue());
    }

    /**
     * @covers Pants\Task\Property::execute
     */
    public function testNameIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->task
            ->setValue('value')
            ->execute();
    }

    /**
     * @covers Pants\Task\Property::execute
     */
    public function testPropertiesAreSetOnTheProjectOnExecute()
    {
        $this->task
            ->setName('one')
            ->setValue('two')
            ->execute();

        $this->assertEquals('two', $this->task->getProject()->getProperties()->one);
    }

}