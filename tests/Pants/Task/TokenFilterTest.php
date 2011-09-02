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
    Pants\Task\TokenFilter,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class TokenFilterTest extends TestCase
{

    /**
     * Temporary file
     * @var string
     */
    protected $_file;

    /**
     * TokenFilter task
     * @var TokenFilter
     */
    protected $_tokenFilter;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->_tokenFilter = new TokenFilter();
        $this->_tokenFilter->setProject(new Project());

        $this->_file = tempnam(sys_get_temp_dir(), "Pants_");
    }

    /**
     * Tear down the test
     */
    public function tearDown()
    {
        unlink($this->_file);
    }

    public function testTokensCanBeAdded()
    {
        $this->_tokenFilter
             ->addReplacement("asdf", "fdsa")
             ->addReplacement("qwer", "rewq");

        $this->assertEquals(array("asdf" => "fdsa", "qwer" => "rewq"), $this->_tokenFilter->getReplacements());
    }

    public function testTokensAreReplacedInTheFileOnExecute()
    {
        $this->markTestIncomplete();
    }

}
