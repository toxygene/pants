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
use Pants\Task\Chdir;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class ChdirTest extends TestCase
{

    /**
     * Current working directory
     * @var string
     */
    protected $cwd;

    /**
     * Chdir task
     * @var Chdir
     */
    protected $task;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->cwd = getcwd();
        
        $this->task = new Chdir();
        $this->task->setProject(new Project());
    }

    /**
     * Tear down the test case
     */
    public function tearDown()
    {
        chdir($this->cwd);
        unset($this->task);
    }

    /**
     * @covers Pants\Task\Chdir::getDirectory
     * @covers Pants\Task\Chdir::setDirectory
     */
    public function testDirectoryCanBeSet()
    {
        $this->task
            ->setDirectory('test');

        $this->assertEquals('test', $this->task->getDirectory());
    }

    /**
     * @covers Pants\Task\Chdir::execute
     */
    public function testDirectoryIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->task
            ->execute();
    }
    
    /**
     * @covers Pants\Task\Chdir::execute
     */
    public function testChdirToInvalidDirectoryThrowsErrorException()
    {
        $this->setExpectedException('\ErrorException');

        $this->task
            ->setDirectory('/8pa8pvoiaKVRa8ij4Da4a90uv89')
            ->execute();
    }
    
    /**
     * @covers Pants\Task\Chdir::execute
     */
    public function testChdirChangesTheCurrentWorkingDirectory()
    {
        $this->task
            ->setDirectory(__DIR__ . '/_files')
            ->execute();
            
        $this->assertEquals(realPath(__DIR__ . '/_files'), getcwd());
    }

}
