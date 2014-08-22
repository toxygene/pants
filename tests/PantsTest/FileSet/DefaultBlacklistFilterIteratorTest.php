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

use FilesystemIterator;
use org\bovigo\vfs\vfsStream;
use Pants\FileSet\DefaultBlacklistFilterIterator;
use PHPUnit_Framework_TestCase as TestCase;
use RecursiveDirectoryIterator;

/**
 *
 */
class DefaultBlacklistFilterIteratorTest extends TestCase
{

    /**
     * Filter
     *
     * @var DefaultBlacklistFilterIterator
     */
    protected $filter;

    /**
     * Set up the test case
     */
    public function setUp()
    {
        vfsStream::setup('root', null, array(
            '.git' => array(),
            '.gitignore' => 'test',
            '.svn' => array(),
            'test' => array()
        ));

        $this->filter = new DefaultBlacklistFilterIterator(new RecursiveDirectoryIterator(
            vfsStream::url('root'),
            FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS
        ));
    }

    /**
     * @covers Pants\FileSet\DefaultBlacklistFilterIterator::getPatterns
     * @covers Pants\FileSet\DefaultBlacklistFilterIterator::setPatterns
     */
    public function testPatternsCanBeSet()
    {
        $this->filter
            ->setPatterns(array('one', 'two'));

        $this->assertEquals(array('one', 'two'), $this->filter->getPatterns());
    }

    /**
     * @covers Pants\FileSet\DefaultBlacklistFilterIterator::getBaseDirectory
     * @covers Pants\FileSet\DefaultBlacklistFilterIterator::setBaseDirectory
     */
    public function testBaseDirectoryCanBeSet()
    {
        $this->filter
            ->setBaseDirectory('test');

        $this->assertEquals('test', $this->filter->getBaseDirectory());
    }

    /**
     * @covers Pants\FileSet\DefaultBlacklistFilterIterator::accept
     */
    public function testOnlyNonIgnoredFilesAreReturned()
    {
        $this->filter
            ->setBaseDirectory(vfsStream::url('root'));

        $results = iterator_to_array($this->filter);

        $this->assertEquals(1, count($results));
        $this->assertContains(vfsStream::url("root/test"), $results);
    }

}
