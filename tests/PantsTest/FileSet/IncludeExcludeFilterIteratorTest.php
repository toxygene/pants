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
use Pants\FileSet\IncludeExcludeFilterIterator;
use PHPUnit_Framework_TestCase as TestCase;
use RecursiveDirectoryIterator;

/**
 *
 */
class IncludeExcludeFilterIteratorTest extends TestCase
{

    /**
     * Filter
     *
     * @var IncludeExcludeFilterIterator
     */
    protected $filter;

    /**
     * Set up the test case
     */
    public function setUp()
    {
        vfsStream::setup('root', null, array(
            'one' => 'test',
            'two' => 'test',
            'three' => 'test',
            'four' => 'test'
        ));

        $this->filter = new IncludeExcludeFilterIterator(new RecursiveDirectoryIterator(
            vfsStream::url('root')
        ));
    }

    /**
     * @covers Pants\FileSet\IncludeExcludeFilterIterator::getBaseDirectory
     * @covers Pants\FileSet\IncludeExcludeFilterIterator::setBaseDirectory
     */
    public function testBaseDirectoryCanBeSet()
    {
        $this->filter->setBaseDirectory(vfsStream::url('one'));

        $this->assertEquals(vfsStream::url('one'), $this->filter->getBaseDirectory());
    }

    /**
     * @covers Pants\FileSet\IncludeExcludeFilterIterator::getExcludes
     * @covers Pants\FileSet\IncludeExcludeFilterIterator::setExcludes
     */
    public function testExcludePatternsCanBeSet()
    {
        $matcher1 = $this->getMock('\Pants\FileSet\IncludeExcludeFilterIterator\Matcher');
        $matcher2 = $this->getMock('\Pants\FileSet\IncludeExcludeFilterIterator\Matcher');

        $this->filter->setExcludes(array($matcher1, $matcher2));

        $this->assertCount(2, $this->filter->getExcludes());
        $this->assertContains($matcher1, $this->filter->getExcludes());
        $this->assertContains($matcher2, $this->filter->getExcludes());
    }

    /**
     * @covers Pants\FileSet\IncludeExcludeFilterIterator::getIncludes
     * @covers Pants\FileSet\IncludeExcludeFilterIterator::setIncludes
     */
    public function testIncludePatternsCanBeSet()
    {
        $matcher1 = $this->getMock('\Pants\FileSet\IncludeExcludeFilterIterator\Matcher');
        $matcher2 = $this->getMock('\Pants\FileSet\IncludeExcludeFilterIterator\Matcher');

        $this->filter->setIncludes(array($matcher1, $matcher2));

        $this->assertCount(2, $this->filter->getIncludes());
        $this->assertContains($matcher1, $this->filter->getIncludes());
        $this->assertContains($matcher2, $this->filter->getIncludes());
    }

    /**
     * @covers Pants\FileSet\IncludeExcludeFilterIterator::accept
     */
    public function testEverythingIsIgnoredByDefault()
    {
        $this->assertEmpty(iterator_to_array($this->filter));
    }

    /**
     * @covers Pants\FileSet\IncludeExcludeFilterIterator::accept
     */
    public function testFilesAreAcceptedIfTheyAreIncludedAndNotExcluded()
    {
        $include = $this->getMock('\Pants\FileSet\IncludeExcludeFilterIterator\Matcher');
        $include->expects($this->at(0))
            ->method('match')
            ->with('one')
            ->will($this->returnValue(true));

        $include->expects($this->at(1))
            ->method('match')
            ->with('two')
            ->will($this->returnValue(true));

        $include->expects($this->at(2))
            ->method('match')
            ->with('three')
            ->will($this->returnValue(false));

        $include->expects($this->at(3))
            ->method('match')
            ->with('four')
            ->will($this->returnValue(true));

        $exclude = $this->getMock('\Pants\FileSet\IncludeExcludeFilterIterator\Matcher');
        $exclude->expects($this->at(0))
            ->method('match')
            ->with('one')
            ->will($this->returnValue(false));
            
        $exclude->expects($this->at(1))
            ->method('match')
            ->with('two')
            ->will($this->returnValue(true));
            
        $exclude->expects($this->at(2))
            ->method('match')
            ->with('four')
            ->will($this->returnValue(false));

        $this->filter
            ->setBaseDirectory(vfsStream::url('root'))
            ->setExcludes(array($exclude))
            ->setIncludes(array($include));

        $results = iterator_to_array($this->filter);

        $this->assertEquals(2, count($results));
        $this->assertContains(vfsStream::url('root/one'), $results);
        $this->assertContains(vfsStream::url('root/four'), $results);
    }

}
