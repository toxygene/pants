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

namespace Pants\Test\Fileset\Fileset\WhitelistBlacklistFilterIterator;

use org\bovigo\vfs\vfsStream;
use Pants\Matcher\RegexpMatcher;
use PHPUnit\Framework\TestCase;
use SplFileInfo;

/**
 * Unit tests for the regex matcher
 * 
 * @coversDefaultClass \Pants\Fileset\Fileset\Regexp
 */
class RegexpTest extends TestCase
{

    /**
     * Regexp matcher
     *
     * @var RegexpMatcher
     */
    protected $matcher;
    
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->matcher = new RegexpMatcher('#^asdf$#');
        
        vfsStream::setup('root', null, array(
            'one' => array(
                'two' => array(
                    'asdf' => '',
                    'qwerty' => ''
                )
            )
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->matcher);
    }
    
    /**
     * @covers ::match
     */
    public function testFilesThatMatchThePatternAreAccepted()
    {
        $this->assertTrue(
            $this->matcher->match(
                new SplFileInfo(vfsStream::url('one/two/asdf')),
                vfsStream::url('one/two')
            )
        );

        $this->assertFalse(
            $this->matcher->match(
                new SplFileInfo(vfsStream::url('one/two/qwerty')),
                vfsStream::url('one/two')
            )
        );
    }

}
