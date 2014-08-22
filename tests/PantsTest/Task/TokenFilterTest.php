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
use Pants\Task\TokenFilter;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class TokenFilterTest extends TestCase
{

    /**
     * Temporary file
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
     * TokenFilter task
     * @var TokenFilter
     */
    protected $tokenFilter;

    /**
     * Setup the test
     */
    public function setUp()
    {
        vfsStream::setup('root', null, array(
            'one' => '@asdf@ $qwer$'
        ));

        $this->file        = vfsStream::url('root/one');
        $this->properties  = $this->getMock('\Pants\Property\Properties');
        $this->tokenFilter = new TokenFilter($this->properties);
    }

    /**
     * Tear down the test
     */
    public function tearDown()
    {
        unset($this->file);
        unset($this->properties);
        unset($this->tokenFilter);
    }

    /**
     * @covers Pants\Task\TokenFilter::execute
     */
    public function testFileIsRequired()
    {
        $this->setExpectedException('\Pants\BuildException');

        $this->tokenFilter
            ->execute();
    }
    
    /**
     * @covers Pants\Task\TokenFilter::getFile
     * @covers Pants\Task\TokenFilter::setFile
     */
    public function testFileCanBeSet()
    {
        $this->tokenFilter
            ->setFile('test');
            
        $this->assertEquals('test', $this->tokenFilter->getFile());
    }

    /**
     * @covers Pants\Task\TokenFilter::addReplacement
     * @covers Pants\Task\TokenFilter::getReplacements
     */
    public function testTokensCanBeAdded()
    {
        $this->tokenFilter
            ->addReplacement('asdf', 'fdsa')
            ->addReplacement('qwer', 'rewq');

        $this->assertEquals(array('asdf' => 'fdsa', 'qwer' => 'rewq'), $this->tokenFilter->getReplacements());
    }

    /**
     * @covers Pants\Task\TokenFilter::__construct
     * @covers Pants\Task\TokenFilter::execute
     */
    public function testTokensAreReplacedInTheFileOnExecute()
    {
        $this->properties
            ->expects($this->at(0))
            ->method('filter')
            ->with('@')
            ->will($this->returnValue('@'));
            
        $this->properties
            ->expects($this->at(1))
            ->method('filter')
            ->with($this->file)
            ->will($this->returnValue($this->file));
            
        $this->properties
            ->expects($this->at(2))
            ->method('filter')
            ->with('@')
            ->will($this->returnValue('@'));
            
        $this->tokenFilter
            ->setFile($this->file)
            ->addReplacement('asdf', 'fdsa')
            ->addReplacement('qwer', 'rewq')
            ->execute();

        $this->assertEquals('fdsa $qwer$', file_get_contents($this->file));
    }

    /**
     * @covers Pants\Task\TokenFilter::getStartingCharacter
     * @covers Pants\Task\TokenFilter::getEndingCharacter
     * @covers Pants\Task\TokenFilter::setStartingCharacter
     * @covers Pants\Task\TokenFilter::setEndingCharacter
     */
    public function testStartAndEndCharacterCanBeChanged()
    {
        $this->tokenFilter
            ->setStartingCharacter('$')
            ->setEndingCharacter('$');

        $this->assertEquals('$', $this->tokenFilter->getStartingCharacter());
        $this->assertEquals('$', $this->tokenFilter->getEndingCharacter());
    }

}
