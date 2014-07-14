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
use Pants\FileSet\WhitelistBlacklistFilterIterator;
use PHPUnit_Framework_TestCase as TestCase;
use RecursiveDirectoryIterator;

/**
 *
 */
class WhitelistBlacklistFilterIteratorTest extends TestCase
{

    /**
     * Filter
     *
     * @var WhitelistBlacklistFilterIterator
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

        $this->filter = new WhitelistBlacklistFilterIterator(new RecursiveDirectoryIterator(
            vfsStream::url('root')
        ));
    }

    /**
     * @covers Pants\FileSet\WhitelistBlacklistFilterIterator::getExcludes
     * @covers Pants\FileSet\WhitelistBlacklistFilterIterator::setExcludes
     */
    public function testBlacklistMatchersCanBeSet()
    {
        $matcher1 = $this->getMock('\Pants\FileSet\WhitelistBlacklistFilterIterator\Matcher');
        $matcher2 = $this->getMock('\Pants\FileSet\WhitelistBlacklistFilterIterator\Matcher');

        $this->filter->setBlacklistMatchers(array($matcher1, $matcher2));

        $this->assertCount(2, $this->filter->getBlacklistMatchers());
        $this->assertContains($matcher1, $this->filter->getBlacklistMatchers());
        $this->assertContains($matcher2, $this->filter->getBlacklistMatchers());
    }

    /**
     * @covers Pants\FileSet\WhitelistBlacklistFilterIterator::getIncludes
     * @covers Pants\FileSet\WhitelistBlacklistFilterIterator::setIncludes
     */
    public function testWhitelistMatchersCanBeSet()
    {
        $matcher1 = $this->getMock('\Pants\FileSet\WhitelistBlacklistFilterIterator\Matcher');
        $matcher2 = $this->getMock('\Pants\FileSet\WhitelistBlacklistFilterIterator\Matcher');

        $this->filter->setWhitelistMatchers(array($matcher1, $matcher2));

        $this->assertCount(2, $this->filter->getWhitelistMatchers());
        $this->assertContains($matcher1, $this->filter->getWhitelistMatchers());
        $this->assertContains($matcher2, $this->filter->getWhitelistMatchers());
    }

    /**
     * @covers Pants\FileSet\WhitelistBlacklistFilterIterator::accept
     */
    public function testEverythingIsIncludedByDefault()
    {
        $this->assertCount(4, $this->filter);
    }

    /**
     * @covers Pants\FileSet\WhitelistBlacklistFilterIterator::accept
     */
    public function testFilesAreAcceptedIfTheyAreIncludedAndNotExcluded()
    {
        $whitelist = $this->getMock('\Pants\FileSet\WhitelistBlacklistFilterIterator\Matcher');

        $whitelist->expects($this->at(0))
            ->method('match')
            ->will($this->returnValue(true));

        $whitelist->expects($this->at(1))
            ->method('match')
            ->will($this->returnValue(true));

        $whitelist->expects($this->at(2))
            ->method('match')
            ->will($this->returnValue(false));

        $whitelist->expects($this->at(3))
            ->method('match')
            ->will($this->returnValue(true));
                    
        $blacklist = $this->getMock('\Pants\FileSet\WhitelistBlacklistFilterIterator\Matcher');

        $blacklist->expects($this->at(0))
            ->method('match')
            ->will($this->returnValue(false));
            
        $blacklist->expects($this->at(1))
            ->method('match')
            ->will($this->returnValue(true));
            
        $blacklist->expects($this->at(2))
            ->method('match')
            ->will($this->returnValue(false));

        $this->filter
            ->setBlacklistMatchers(array($blacklist))
            ->setWhitelistMatchers(array($whitelist));

        $results = iterator_to_array($this->filter);

        $this->assertEquals(2, count($results));
        $this->assertContains(vfsStream::url('root/one'), $results);
        $this->assertContains(vfsStream::url('root/four'), $results);
    }

}
