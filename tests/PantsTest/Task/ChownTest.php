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
use Pants\Task\Chown;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class ChownTest extends TestCase
{

    /**
     * Chown task
     * @var Chown
     */
    protected $chown;
    
    /**
     * File to chown
     * @var string
     */
    protected $file;

    /**
     * Properties mock object
     *
     * @var \Pats\Property\Properties
     */
    protected $properties;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        vfsStream::setup('root', null, array(
            'one' => 'test'
        ));

        $this->file = vfsStream::url('root/one');
        
        $this->properties = $this->getMock('\Pants\Property\Properties');
        
        $this->chown = new Chown($this->properties);
    }

    /**
     * Tear down the test case
     */
    public function tearDown()
    {
        unset($this->chown);
        unset($this->file);
        unset($this->properties);
    }

    /**
     * @covers Pants\Task\Chown::execute
     */
    public function testFileIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->chown
            ->setOwner(1000)
            ->execute();
    }

    /**
     * @covers Pants\Task\Chown::execute
     */
    public function testOwnerIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->chown
            ->setFile($this->file)
            ->execute();
    }

    /**
     * @covers Pants\Task\Chown::__construct
     * @covers Pants\Task\Chown::execute
     */
    public function testOwnerIsSet()
    {
        $this->properties
            ->expects($this->at(0))
            ->method('filter')
            ->with(1000)
            ->will($this->returnArgument(0));
            
        $this->properties
            ->expects($this->at(1))
            ->method('filter')
            ->with($this->file)
            ->will($this->returnArgument(0));
            
        $this->chown
            ->setFile($this->file)
            ->setOwner(1000)
            ->execute();

        $this->assertEquals(1000, fileowner($this->file));
    }

}
