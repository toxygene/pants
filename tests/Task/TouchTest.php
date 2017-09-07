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

use org\bovigo\vfs\vfsStream;
use Pants\Project;
use Pants\Property\Properties;
use Pants\Task\Touch;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the touch task
 *
 * @covers \Pants\Task\Touch
 */
class TouchTest extends TestCase
{

    /**
     * File to touch
     * @var string
     */
    protected $file;

    /**
     * Touch task
     * @var Touch
     */
    protected $touch;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        
        vfsStream::setup('root', null, array(
            'one' => 'test'
        ));
        
        $this->file  = vfsStream::url('root/one');
        $this->touch = new Touch();
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();
        
        unset($this->file);
        unset($this->touch);
    }
    
    /**
     * @covers ::getFile
     * @covers ::setFile
     */
    public function testFileCanBeSet()
    {
        $this->touch
            ->setPath('test');

        $this->assertEquals('test', $this->touch->getPath());
    }
    
    /**
     * @covers ::getTime
     * @covers ::setTime
     */
    public function testTimeCanBeSet()
    {
        $this->touch
            ->setTime(10001);

        $this->assertEquals(10001, $this->touch->getTime());
    }

    /**
     * @covers ::execute
     * @expectedException \Pants\BuildException
     */
    public function testFileIsRequired()
    {
        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        $this->touch
            ->execute($mockProject);
    }

    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testTouchingANonExistentFileCreatesItAndSetsTheModifiedTime()
    {
        $file = vfsStream::url('root') . DIRECTORY_SEPARATOR . 'two';
        $time = time();

        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        /** @var Properties|\PHPUnit_Framework_MockObject_MockObject $mockProperties */
        $mockProperties = $this->createMock(Properties::class);

        $mockProject->expects($this->exactly(2))
            ->method('getProperties')
            ->will($this->returnValue($mockProperties));

        $mockProperties->expects($this->any())
            ->method('filter')
            ->with($this->anything())
            ->will($this->returnArgument(0));

        $this->touch
            ->setPath($file)
            ->setTime($time)
            ->execute($mockProject);

        $this->assertTrue(file_exists($file));
        $this->assertEquals($time, filemtime($file));
    }

}
