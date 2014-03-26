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
use Pants\Task\Chmod;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class ChmodTest extends TestCase
{

    /**
     * Chmod task
     * @var Chmod
     */
    protected $chmod;
    
    /**
     * File to chmod
     * @var string
     */
    protected $file;

    /**
     * Set up the test case
     */
    public function setUp()
    {
        vfsStream::setup('root', null, array(
            'one' => 'test'
        ));
        
        $this->chmod = new Chmod();
        $this->chmod->setProject(new Project());

        $this->file = vfsStream::url('root/one');
    }

    /**
     * Tear down the test
     */
    public function tearDown()
    {
        unset($this->chmod);
        unset($this->file);
    }

    /**
     * @covers Pants\Task\Chmod::execute
     */
    public function testFileIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->chmod
            ->setMode('0777')
            ->execute();
    }

    /**
     * @covers Pants\Task\Chmod::execute
     */
    public function testModeIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->chmod
            ->setFile($this->file)
            ->execute();
    }

    /**
     * @covers Pants\Task\Chmod::execute
     */
    public function testPermissionsIsSet()
    {
        $this->chmod
            ->setFile($this->file)
            ->setMode(0654)
            ->execute();

        $this->assertTrue((fileperms($this->file) & 0777) === 0654);
    }

    /**
     * @covers Pants\Task\Chmod::execute
     */
    public function testPermissionsAsAStringCanBeSet()
    {
        $this->chmod
            ->setFile($this->file)
            ->setMode("654")
            ->execute();

        $this->assertTrue((fileperms($this->file) & 0777) === 0654);
    }

}
