<?php
/**
 * Pants
 *
 * Copyright (c) 2011-2017, Justin Hendrickson
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

namespace Pants\Test\Task;

use org\bovigo\vfs\vfsStream;
use Pants\Task\TokenFilter;

/**
 * Unit tests for the token filter task
 *
 * @coversDefaultClass \Pants\Task\TokenFilter
 */
class TokenFilterTest extends TaskTestCase
{

    /**
     * Temporary file
     * @var string
     */
    protected $file;

    /**
     * TokenFilter task
     * @var TokenFilter
     */
    protected $tokenFilter;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        
        vfsStream::setup('root', null, array(
            'one' => '@asdf@ $qwer$'
        ));

        $this->file        = vfsStream::url('root/one');
        $this->tokenFilter = new TokenFilter();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();
        
        unset($this->file);
        unset($this->tokenFilter);
    }

    /**
     * @covers ::execute
     * @expectedException \Pants\Task\BuildException
     */
    public function testFileIsRequired()
    {
        $this->tokenFilter
            ->execute($this->mockContext);
    }
    
    /**
     * @covers ::getFile
     * @covers ::setFile
     */
    public function testFileCanBeSet()
    {
        $this->tokenFilter
            ->setFile('test');
            
        $this->assertEquals('test', $this->tokenFilter->getFile());
    }

    /**
     * @covers ::addReplacement
     * @covers ::getReplacements
     */
    public function testTokensCanBeAdded()
    {
        $this->tokenFilter
            ->addReplacement('asdf', 'fdsa')
            ->addReplacement('qwer', 'rewq');

        $this->assertEquals(array('asdf' => 'fdsa', 'qwer' => 'rewq'), $this->tokenFilter->getReplacements());
    }

    /**
     * @covers ::execute
     */
    public function testTokensAreReplacedInTheFileOnExecute()
    {
        $this->tokenFilter
            ->setFile($this->file)
            ->addReplacement('asdf', 'fdsa')
            ->addReplacement('qwer', 'rewq')
            ->execute($this->mockContext);

        $this->assertEquals('fdsa $qwer$', file_get_contents($this->file));
    }

    /**
     * @covers ::getStartingCharacter
     * @covers ::getEndingCharacter
     * @covers ::setStartingCharacter
     * @covers ::setEndingCharacter
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
