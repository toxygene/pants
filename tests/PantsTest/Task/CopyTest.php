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

use org\bovigo\vfs\vfsStream;
use Pants\Task\Copy;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class CopyTest extends TestCase
{

    /**
     * File to copy
     * @var string
     */
    protected $file;
    
    /**
     * Properties mock object
     *
     * @var \Pants\Property\Properties
     */
    protected $properties;

    /**
     * Copy task
     * @var Copy
     */
    protected $task;

    /**
     * Setup the test
     */
    public function setUp()
    {
        vfsStream::setup('root', null, array(
            'test' => 'testing'
        ));

        $this->file = vfsStream::url('root/test');
        
        $this->properties = $this->getMock('\Pants\Property\Properties');
        
        $this->task = new Copy($this->properties);
    }

    /**
     * Tear down the test
     */
    public function tearDown()
    {
        unset($this->file);
        unset($this->properties);
        unset($this->task);
    }

    /**
     * @covers Pants\Task\Copy::execute
     */
    public function testFileIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->task
            ->setDestination($this->file . '_1')
            ->execute();
    }

    /**
     * @covers Pants\Task\Copy::execute
     */
    public function testDestinationIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->task
            ->setFile($this->file)
            ->execute();
    }

    /**
     * @covers Pants\Task\Copy::__construct
     * @covers Pants\Task\Copy::execute
     */
    public function testFileIsCopied()
    {
        $source = $this->file;
        $destination = $this->file . '_1';
        
        $this->properties
            ->expects($this->at(0))
            ->method('filter')
            ->with($source)
            ->will($this->returnArgument(0));
        
        $this->properties
            ->expects($this->at(1))
            ->method('filter')
            ->with($destination)
            ->will($this->returnArgument(0));
        
        $this->task
            ->setFile($source)
            ->setDestination($destination)
            ->execute();

        $this->assertTrue(file_exists($destination));
        $this->assertEquals('testing', file_get_contents($destination));
    }

}
