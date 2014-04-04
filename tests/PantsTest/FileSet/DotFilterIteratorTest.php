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

namespace PantsTest\FileSet;

use org\bovigo\vfs\vfsStream;
use Pants\FileSet\DotFilterIterator;
use PHPUnit_Framework_TestCase as TestCase;
use RecursiveDirectoryIterator;
use SplFileInfo;

/**
 *
 */
class DotFilterIteratorTest extends TestCase
{

    /**
     * Filter iterator
     *
     * @var DotFilterIterator
     */
    protected $filter;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        vfsStream::setup('root', null, array(
            '.' => array(),
            '..' => array(),
            'one' => 'one'
        ));
        
        $this->filter = new DotFilterIterator(
            new RecursiveDirectoryIterator(
                vfsStream::url('root')
            )
        );
    }
    
    /**
     * Tear down the test case
     */
    public function tearDown()
    {
        unset($this->filter);
    }

    /**
     * @covers Pants\FileSet\DotFilterIterator::accept
     */
    public function testDotDirectoriesAreFilteredOut()
    {
        $results = iterator_to_array($this->filter);

        $this->assertCount(1, $results);
        $this->assertArrayHasKey(vfsStream::url('root/one'), $results);
        $this->assertEquals(new SplFileInfo(vfsStream::url('root/one')), $results[vfsStream::url('root/one')]);
    }

}
