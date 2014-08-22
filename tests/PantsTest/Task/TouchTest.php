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
use Pants\Task\Touch;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Unit tests for the touch task
 */
class TouchTest extends TestCase
{

    /**
     * File to touch
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
     * Touch task
     * @var Touch
     */
    protected $touch;

    /**
     * Setup the test
     */
    public function setUp()
    {
        vfsStream::setup('root', null, array(
            'one' => 'test'
        ));
        
        $this->file       = vfsStream::url('root/one');
        $this->properties = $this->getMock('\Pants\Property\Properties');
        $this->touch      = new Touch($this->properties);
    }
    
    /**
     * Tear down the test case
     */
    public function tearDown()
    {
        unset($this->file);
        unset($this->properties);
        unset($this->touch);
    }
    
    /**
     * @covers Pants\Task\Touch::getFile
     * @covers Pants\Task\Touch::setFile
     */
    public function testFileCanBeSet()
    {
        $this->touch
            ->setFile('test');

        $this->assertEquals('test', $this->touch->getFile());
    }
    
    /**
     * @covers Pants\Task\Touch::getTime
     * @covers Pants\Task\Touch::setTime
     */
    public function testTimeCanBeSet()
    {
        $this->touch
            ->setTime(10001);

        $this->assertEquals(10001, $this->touch->getTime());
    }

    /**
     * @covers Pants\Task\Touch::execute
     */
    public function testFileIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->touch
            ->execute();
    }

    /**
     * @covers Pants\Task\Touch::__construct
     * @covers Pants\Task\Touch::execute
     */
    public function testTouchingANonExistentFileCreatesItAndSetsTheModifiedTime()
    {
        $file = vfsStream::url('root') . DIRECTORY_SEPARATOR . 'two';
        $time = time();

        $this->properties
            ->expects($this->at(0))
            ->method('filter')
            ->with($file)
            ->will($this->returnArgument(0));

        $this->properties
            ->expects($this->at(1))
            ->method('filter')
            ->with($time)
            ->will($this->returnArgument(0));

        $this->touch
            ->setFile($file)
            ->setTime($time)
            ->execute();

        $this->assertTrue(file_exists($file));
        $this->assertEquals($time, filemtime($file));
    }

}
