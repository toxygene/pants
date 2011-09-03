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
    Pants\Task\Chown,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class ChownTest extends TestCase
{

    /**
     * Chown task
     * @var Chown
     */
    protected $_chown;

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
        if (!PANTS_CHOWN_VALID_USER_NAME) {
            $this->markTestSkipped("PANTS_CHOWN_VALID_USER_NAME constant is not set");
        }

        if (!PANTS_CHOWN_INVALID_USER_NAME) {
            $this->markTestSkipped("PANTS_CHOWN_INVALID_USER_NAME constant is not set");
        }

        $this->_chown = new Chown();
        $this->_chown->setProject(new Project());

        $this->_file = tempnam(sys_get_temp_dir(), "Pants_");
    }

    /**
     * Tear down the test
     */
    public function tearDown()
    {
        unlink($this->_file);
    }

    public function testFailureRaisesABuildException()
    {
        $this->setExpectedException("\Pants\BuildException");

        $this->_chown
             ->setFile($this->_file)
             ->setOwner(PANTS_CHOWN_INVALID_USER_NAME)
             ->execute();
    }

    public function testOwnerIsSetOnTheFileObject()
    {
        $this->_chown
             ->setFile($this->_file)
             ->setOwner(PANTS_CHOWN_VALID_USER_NAME)
             ->execute();

        $this->markTestIncomplete();
    }

}
