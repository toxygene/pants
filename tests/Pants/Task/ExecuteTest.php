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
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
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

use Pants\Project,
    Pants\Task\Execute,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class ExecuteTest extends TestCase
{

    /**
     * Current working directory
     * @var string
     */
    protected $_cwd;

    /**
     * Execute task
     * @var Delete
     */
    protected $_execute;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->_execute = new Execute();
        $this->_execute->setProject(new Project());

        $this->_cwd = getcwd();
    }

    /**
     * Tear down the test
     */
    public function tearDown()
    {
        chdir($this->_cwd);
    }

    public function testCommandIsRequired()
    {
        $this->setExpectedException("\Pants\BuildException");

        $this->_execute
             ->execute();
    }

    public function testFailureThrowsABuildException()
    {
        $this->setExpectedException("\Pants\BuildException");

        $this->_execute
             ->execute();
    }

    public function testExecuteRunsCommand()
    {
        $directory = sys_get_temp_dir();
        $file      = "{$directory}/asdfasdf";

        $command = sprintf("%s %s", escapeshellcmd("touch"), escapeshellarg($file));

        $this->_execute
             ->setDirectory($directory)
             ->setCommand($command)
             ->execute();

        $this->assertTrue(file_exists($file));

        unlink($file);
    }

}
