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
use Pants\FileSet\IncludeExcludeFilterIterator;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class IncludeExcludeFilterIteratorTest extends TestCase
{

    public function testBaseDirectoryCanBeSet()
    {
        $filter = new IncludeExcludeFilterIterator(new ArrayIterator());

        $filter->setBaseDirectory('test');

        $this->assertEquals('test', $filter->getBaseDirectory());
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
        $mocks = $this->_getMockFileObjects();

        $filter = new IncludeExcludeFilterIterator(new ArrayIterator($mocks));

        $this->assertEmpty(iterator_to_array($filter));
    }

    public function testFilesAreAcceptedIfTheyAreIncludedAndNotExcluded()
    {
        $mocks = $this->_getMockFileObjects();

        $filter = new IncludeExcludeFilterIterator(new ArrayIterator($mocks));
        $filter->setBaseDirectory('/a/b')
               ->setExcludes(array('#^t#'))
               ->setIncludes(array('#o#'));

        $results = iterator_to_array($filter);

        $this->assertEquals(2, count($results));
        $this->assertContains($mocks['one'], $results);
        $this->assertContains($mocks['four'], $results);
    }

    protected function _getMockFileObjects()
    {
        $one = $this->getMock('SplFileObject', array(), array('/a/b/one'), '', true);
        $one->expects($this->once())
            ->method('getPathname')
            ->will($this->returnValue('/a/b/one'));

        $two = $this->getMock('SplFileObject', array(), array('/a/b/two'), '', true);
        $two->expects($this->once())
            ->method('getPathname')
            ->will($this->returnValue('/a/b/two'));

        $three = $this->getMock('SplFileObject', array(), array('/a/b/three'), '', true);
        $three->expects($this->once())
              ->method('getPathname')
              ->will($this->returnValue('/a/b/three'));

        $four = $this->getMock('SplFileObject', array(), array('/a/b/four'), '', true);
        $four->expects($this->once())
             ->method('getPathname')
             ->will($this->returnValue('/a/b/four'));

        return array(
            'one' => $one,
            'two' => $two,
            'three' => $three,
            'four' => $four
        );
    }

}
