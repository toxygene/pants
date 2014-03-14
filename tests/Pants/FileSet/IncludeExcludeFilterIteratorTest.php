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

use ArrayIterator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\visitor\vfsStreamPrintVisitor;
use Pants\FileSet\IncludeExcludeFilterIterator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class IncludeExcludeFilterIteratorTest extends TestCase
{

    /**
     * Virtual file system
     *
     * @var vfsStream
     */
    protected $vfs;

    /**
     * Set up the test case
     */
    public function setUp()
    {
        $this->vfs = vfsStream::setup('root', null, array(
            'one' => 'test',
            'two' => 'test',
            'three' => 'test',
            'four' => 'test'
        ));
    }

    public function testBaseDirectoryCanBeSet()
    {
        $filter = new IncludeExcludeFilterIterator(new ArrayIterator());

        $filter->setBaseDirectory(vfsStream::url('a'));

        $this->assertEquals(vfsStream::url('a'), $filter->getBaseDirectory());
    }

    public function testExcludePatternsCanBeSet()
    {
        $filter = new IncludeExcludeFilterIterator(new ArrayIterator());

        $filter->setExcludes(array('one', 'two'));

        $this->assertEquals(array('one', 'two'), $filter->getExcludes());
    }

    public function testIncludePatternsCanBeSet()
    {
        $filter = new IncludeExcludeFilterIterator(new ArrayIterator());

        $filter->setIncludes(array('one', 'two'));

        $this->assertEquals(array('one', 'two'), $filter->getIncludes());
    }

    public function testEverythingIsIgnoredByDefault()
    {
        $filter = new IncludeExcludeFilterIterator(new ArrayIterator(array(
            'one' => 'one',
            'two' => 'two',
            'three' => 'three',
            'four' => 'four'
        )));

        $this->assertEmpty(iterator_to_array($filter));
    }

    public function testFilesAreAcceptedIfTheyAreIncludedAndNotExcluded()
    {
        $filter = new IncludeExcludeFilterIterator(new ArrayIterator(array(
            'one',
            'two',
            'three',
            'four'
        )));
        
        $filter->setBaseDirectory(vfsStream::url('root'))
               ->setExcludes(array('#^t#'))
               ->setIncludes(array('#o#'));

        $results = iterator_to_array($filter);

        $this->assertEquals(2, count($results));
        $this->assertContains('one', $results);
        $this->assertContains('four', $results);
    }

}
