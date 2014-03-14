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
use Pants\Project;
use Pants\Task\Touch;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
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
     * Virtual file system
     * @var vfsStream
     */
    protected $vfs;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->touch = new Touch();
        $this->touch->setProject(new Project());
        
        $this->vfs = vfsStream::setup('root', null, array(
            'one' => 'test'
        ));
        
        $this->file = vfsStream::url('root/one');
    }

    public function testFileIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->touch
            ->execute();
    }

    public function testTouchingANonExistentFileCreatesItAndSetsTheModifiedTime()
    {
        $time = time();

        $this->touch
            ->setFile($this->file)
            ->setTime($time)
            ->execute();

        $this->assertTrue(file_exists($this->file));
        $this->assertEquals($time, filemtime($this->file));
    }

}
