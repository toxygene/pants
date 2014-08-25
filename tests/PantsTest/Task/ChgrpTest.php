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
use Pants\Task\Chgrp;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Unit tests for the Chgrp task
 */
class ChgrpTest extends TestCase
{

    /**
     * Chgrp task
     * @var Chgrp
     */
    protected $chgrp;

    /**
     * File to chgrp
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
     * Setup the test case
     */
    public function setUp()
    {
        vfsStream::setup('root', null, array(
            'test' => 'test'
        ));

        $this->file = vfsStream::url('root/test');

        $this->properties = $this->getMock('\Pants\Property\Properties');

        $this->chgrp = new Chgrp($this->properties);
    }

    /**
     * Tear down the test case
     */
    public function tearDown()
    {
        unset($this->chgrp);
        unset($this->file);
        unset($this->properties);
    }

    /**
     * @covers Pants\Task\Chgrp::execute
     */
    public function testFileIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->chgrp
            ->setGroup(1000)
            ->execute();
    }

    /**
     * @covers Pants\Task\Chgrp::execute
     */
    public function testGroupIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');
        
        $this->chgrp
            ->setFile($this->file)
            ->execute();
    }


    /**
     * @covers Pants\Task\Chgrp::__construct
     * @covers Pants\Task\Chgrp::execute
     */
    public function testGroupIsSet()
    {
        $this->properties
            ->expects($this->at(0))
            ->method('filter')
            ->with(1000)
            ->will($this->returnArgument(0));

        $this->properties
            ->expects($this->at(1))
            ->method('filter')
            ->with($this->file)
            ->will($this->returnArgument(0));

        $this->chgrp
            ->setFile($this->file)
            ->setGroup(1000)
            ->execute();

        $this->assertEquals(1000, filegroup($this->file));
    }

}
