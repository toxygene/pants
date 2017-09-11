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

namespace Pants\Test\Fileset\Fileset;

use ArrayIterator;
use FilesystemIterator;
use org\bovigo\vfs\vfsStream;
use Pants\Fileset\Fileset\MatcherInterface;
use Pants\Fileset\Fileset\Matchers;
use Pants\Fileset\Fileset\WhitelistBlacklistFilterIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;

/**
 * @coversDefaultClass \Pants\FileSet\WhitelistBlacklistFilterIterator
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
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        vfsStream::setup('root', null, array(
            'one' => 'test',
            'two' => 'test',
            'three' => 'test',
            'four' => 'test'
        ));

        $this->filter = new WhitelistBlacklistFilterIterator(new RecursiveDirectoryIterator(
            vfsStream::url('root'),
            FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->filter);
    }

    /**
     * @covers ::getBlacklistMatchers
     * @covers ::setBlacklistMatchers
     */
    public function testBlacklistMatchersCanBeSet()
    {
        /** @var MatcherInterface|\PHPUnit_Framework_MockObject_MockObject $matcher1 */
        $matcher1 = $this->createMock(MatcherInterface::class);

        /** @var MatcherInterface|\PHPUnit_Framework_MockObject_MockObject $matcher2 */
        $matcher2 = $this->createMock(MatcherInterface::class);

        /** @var Matchers|\PHPUnit_Framework_MockObject_MockObject $matchers */
        $matchers = $this->createMock(Matchers::class);

        $matchers->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator([$matcher1, $matcher2])));

        $this->filter->setBlacklist($matchers);

        $this->assertCount(2, $this->filter->getBlacklist());
        $this->assertContains($matcher1, $this->filter->getBlacklist());
        $this->assertContains($matcher2, $this->filter->getBlacklist());
    }

    /**
     * @covers ::getWhitelistMatchers
     * @covers ::setWhitelistMatchers
     */
    public function testWhitelistMatchersCanBeSet()
    {
        /** @var MatcherInterface|\PHPUnit_Framework_MockObject_MockObject $matcher1 */
        $matcher1 = $this->createMock(MatcherInterface::class);

        /** @var MatcherInterface|\PHPUnit_Framework_MockObject_MockObject $matcher2 */
        $matcher2 = $this->createMock(MatcherInterface::class);

        /** @var Matchers|\PHPUnit_Framework_MockObject_MockObject $matchers */
        $matchers = $this->createMock(Matchers::class);

        $matchers->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator([$matcher1, $matcher2])));

        $this->filter->setWhitelist($matchers);

        $this->assertCount(2, $this->filter->getWhitelist());
        $this->assertContains($matcher1, $this->filter->getWhitelist());
        $this->assertContains($matcher2, $this->filter->getWhitelist());
    }

    /**
     * @covers ::accept
     */
    public function testEverythingIsIncludedByDefault()
    {
        $this->assertCount(4, $this->filter);
    }

    /**
     * @covers ::accept
     */
    public function testFilesAreAcceptedIfTheyAreIncludedAndNotExcluded()
    {
        /** @var MatcherInterface|\PHPUnit_Framework_MockObject_MockObject $mockWhitelistMatcher */
        $mockWhitelistMatcher = $this->createMock(MatcherInterface::class);

        $mockWhitelistMatcher->expects($this->at(0))
            ->method('match')
            ->will($this->returnValue(true));

        $mockWhitelistMatcher->expects($this->at(1))
            ->method('match')
            ->will($this->returnValue(true));

        $mockWhitelistMatcher->expects($this->at(2))
            ->method('match')
            ->will($this->returnValue(false));

        $mockWhitelistMatcher->expects($this->at(3))
            ->method('match')
            ->will($this->returnValue(true));

        /** @var Matchers|\PHPUnit_Framework_MockObject_MockObject $mockWhitelist */
        $mockWhitelist = $this->createMock(Matchers::class);

        $mockWhitelist->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator([$mockWhitelistMatcher])));

        /** @var MatcherInterface|\PHPUnit_Framework_MockObject_MockObject $mockBlacklistMatcher */
        $mockBlacklistMatcher = $this->createMock(MatcherInterface::class);

        $mockBlacklistMatcher->expects($this->at(0))
            ->method('match')
            ->will($this->returnValue(false));
            
        $mockBlacklistMatcher->expects($this->at(1))
            ->method('match')
            ->will($this->returnValue(true));
            
        $mockBlacklistMatcher->expects($this->at(2))
            ->method('match')
            ->will($this->returnValue(false));

        /** @var Matchers|\PHPUnit_Framework_MockObject_MockObject $mockBlacklist */
        $mockBlacklist = $this->createMock(Matchers::class);

        $mockBlacklist->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator([$mockBlacklistMatcher])));

        $this->filter
            ->setBlacklist($mockBlacklist)
            ->setWhitelist($mockWhitelist);

        $results = iterator_to_array($this->filter);

        $this->assertEquals(2, count($results));
        $this->assertContains(vfsStream::url('root/one'), $results);
        $this->assertContains(vfsStream::url('root/four'), $results);
    }
    
    /**
     * @covers ::accept
     */
    public function testFilesAreRejectedIfTheyAreExcluded()
    {
        /** @var MatcherInterface|\PHPUnit_Framework_MockObject_MockObject $mockBlacklistMatcher */
        $mockBlacklistMatcher = $this->createMock(MatcherInterface::class);

        $mockBlacklistMatcher->expects($this->at(0))
            ->method('match')
            ->will($this->returnValue(true));

        $mockBlacklistMatcher->expects($this->at(1))
            ->method('match')
            ->will($this->returnValue(true));

        $mockBlacklistMatcher->expects($this->at(2))
            ->method('match')
            ->will($this->returnValue(false));

        $mockBlacklistMatcher->expects($this->at(3))
            ->method('match')
            ->will($this->returnValue(true));

        /** @var Matchers|\PHPUnit_Framework_MockObject_MockObject $mockBlacklist */
        $mockBlacklist = $this->createMock(Matchers::class);

        $mockBlacklist->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator([$mockBlacklistMatcher])));
            
        $this->filter
            ->setBlacklist($mockBlacklist);

        $results = iterator_to_array($this->filter);
        
        $this->assertEquals(1, count($results));
        $this->assertContains(vfsStream::url('root/three'), $results);
    }

}
