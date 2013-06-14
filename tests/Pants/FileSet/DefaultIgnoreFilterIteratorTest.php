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

namespace PantsTest\FileSet;

use Pants\FileSet\DefaultIgnoreFilterIterator;
use PHPUnit_Framework_TestCase as TestCase;
use RecursiveArrayIterator;

/**
 *
 */
class DefaultIgnoreFilterIteratorTest extends TestCase
{

    public function testPatternsCanBeSet()
    {
        $filter = new DefaultIgnoreFilterIterator(new RecursiveArrayIterator(array()));

        $filter->setPatterns(array("one", "two"));

        $this->assertEquals(array("one", "two"), $filter->getPatterns());
    }

    public function testOnlyNonIgnoredFilesAreReturned()
    {
        $mocks = $this->_getMockFileObjects();

        $filter = new DefaultIgnoreFilterIterator(new RecursiveArrayIterator($mocks));

        $results = iterator_to_array($filter);

        $this->assertEquals(2, count($results));
        $this->assertContains($mocks["two"], $results);
        $this->assertContains($mocks["four"], $results);
    }

    protected function _getMockFileObjects()
    {
        $one = $this->getMock("SplFileObject", array(), array(), '', false);
        $one->expects($this->once())
            ->method("getFilename")
            ->will($this->returnValue(".git"));

        $two = $this->getMock("SplFileObject", array(), array(), '', false);
        $two->expects($this->once())
            ->method("getFilename")
            ->will($this->returnValue(".gitignore"));

        $three = $this->getMock("SplFileObject", array(), array(), '', false);
        $three->expects($this->once())
              ->method("getFilename")
              ->will($this->returnValue(".svn"));

        $four = $this->getMock("SplFileObject", array(), array(), '', false);
        $four->expects($this->once())
             ->method("getFilename")
             ->will($this->returnValue("test"));

        return array(
            "one" => $one,
            "two" => $two,
            "three" => $three,
            "four" => $four
        );
    }

}
