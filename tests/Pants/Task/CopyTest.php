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
    Pants\Task\Copy,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class CopyTest extends TestCase
{

    /**
     * Copy task
     * @var Copy
     */
    protected $_copy;

    /**
     * Temporary file
     * @var string
     */
    protected $_file;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->_copy = new Copy();
        $this->_copy->setProject(new Project());

        $this->_file = tempnam(sys_get_temp_dir(), "Pants_");
        file_put_contents($this->_file, "testing");
    }

    /**
     * Tear down the test
     */
    public function tearDown()
    {
        unlink($this->_file);
    }

    public function testCopyFailureThrowsABuildException()
    {
        if (!PANTS_COPY_INVALID_PATH) {
            $this->markTestSkipped("PANTS_COPY_INVALID_PATH not set");
        }

        $this->setExpectedException("\Pants\BuildException");

        $this->_copy
             ->setFile($this->_file)
             ->setDestination(PANTS_COPY_INVALID_PATH)
             ->execute();
    }

    public function testFileIsCopied()
    {
        $this->_copy
             ->setFile($this->_file)
             ->setDestination($this->_file . "_1")
             ->execute();

        $this->assertTrue(file_exists($this->_file . "_1"));
        $this->assertEquals("testing", file_get_contents($this->_file . "_1"));

        unlink($this->_file . "_1");
    }

}
