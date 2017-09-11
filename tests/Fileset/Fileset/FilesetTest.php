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

use org\bovigo\vfs\vfsStream;
use Pants\ContextInterface;
use Pants\Fileset\Fileset;
use Pants\Property\PropertiesInterface;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Pants\FileSet\FileSet
 */
class FilesetTest extends TestCase
{
    
    /**
     * File set
     *
     * @var Fileset
     */
    protected $fileset;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        vfsStream::setup(
            'root',
            null,
            array(
                '.git' => array(),
                '.gitignore' => 'test',
                'README' => 'test',
                'src' => array(
                    'test' => 'test',
                    '.test.swp' => 'test'
                )
            )
        );

        $this->fileset = new Fileset();
        $this->fileset->setBaseDirectory(vfsStream::url('root'));
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->fileSet);
    }
    
    /**
     * @covers ::getIterator
     */
    public function testIteratesOverAllFilesAndDirectories()
    {
        /** @var ContextInterface|\PHPUnit_Framework_MockObject_MockObject $mockContext */
        $mockContext = $this->createMock(ContextInterface::class);

        $mockProperties = $this->createMock(PropertiesInterface::class);

        $mockContext->expects($this->any())
            ->method('getProperties')
            ->will($this->returnValue($mockProperties));

        $mockProperties->expects($this->any())
            ->method('filter')
            ->with($this->anything())
            ->will($this->returnArgument(0));

        $paths = iterator_to_array($this->fileset->getIterator($mockContext));

        $this->assertCount(6, $paths);
        $this->assertContains(vfsStream::url('root/.git'), $paths);
        $this->assertContains(vfsStream::url('root/.gitignore'), $paths);
        $this->assertContains(vfsStream::url('root/README'), $paths);
        $this->assertContains(vfsStream::url('root/src'), $paths);
        $this->assertContains(vfsStream::url('root/src/test'), $paths);
        $this->assertContains(vfsStream::url('root/src/.test.swp'), $paths);
    }

}
