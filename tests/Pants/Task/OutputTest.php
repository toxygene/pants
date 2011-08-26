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
    Pants\Task\Output,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class OutputTest extends TestCase
{

    /**
     * Output task
     * @var Output
     */
    protected $_task;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->_task = new Output();
        $this->_task->setProject(new Project());
    }

    public function testMessageCanBeSet()
    {
        $this->_task->setMessage("one");
        $this->assertEquals("one", $this->_task->getMessage());
    }

    public function testMessageIsPrintedOnExecute()
    {
        $this->_task->setMessage("one");

        ob_start();
        $this->_task->execute();
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals("one", $output);
    }

}
