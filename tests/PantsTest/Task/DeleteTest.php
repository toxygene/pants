<?php
/**
 * Pants
 *
 * Copyright (c) 2014, Justin Hendrickson
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
use Pants\Task\Delete;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class DeleteTest extends TestCase
{
    
    /**
     * File to delete
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
     * Delete task
     * @var Delete
     */
    protected $task;

    /**
     * Setup the test
     */
    public function setUp()
    {
        vfsStream::setup('root', null, array(
            'test' => 'test'
        ));
        
        $this->file = vfsStream::url('root/test');
        
        $this->properties = $this->getMock('\Pants\Property\Properties');
        
        $this->task = new Delete($this->properties);
    }

    /**
     * @covers Pants\Task\Delete::execute
     */
    public function testFileIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->task
            ->execute();
    }

    /**
     * @covers Pants\Task\Delete::execute
     */
    public function testFailureThrowsABuildException()
    {
        $this->setExpectedException('\ErrorException');

        $this->task
            ->setFile('something-that-does-not-exist')
            ->execute();
    }

    /**
     * @covers Pants\Task\Delete::__construct
     * @covers Pants\Task\Delete::execute
     */
    public function testFileIsDeleted()
    {
        $this->properties
            ->expects($this->once())
            ->method('filter')
            ->with($this->file)
            ->will($this->returnArgument(0));

        $this->task
            ->setFile($this->file)
            ->execute();
             
        $this->assertFalse(file_exists($this->file));
    }

}
