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

namespace PantsTest\Task;

use org\bovigo\vfs\vfsStream;
use Pants\Project;
use Pants\Property\Properties;
use Pants\Task\Chgrp;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the Chgrp task
 * 
 * @coversDefaultClass \Pants\Task\Chgrp
 */
class ChgrpTest extends TestCase
{

    /**
     * Chgrp task
     * @var Chgrp
     */
    protected $chgrp;

    /**
     * File to chgrp
     * @var string
     */
    protected $file;
    
    /**
     * Properties mock object
     *
     * @var \Pants\Property\Properties
     */
    protected $properties;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        
        vfsStream::setup('root', null, array(
            'test' => 'test'
        ));

        $this->file = vfsStream::url('root/test');

        $this->chgrp = new Chgrp();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();
        
        unset($this->chgrp);
        unset($this->file);
        unset($this->properties);
    }

    /**
     * @covers ::execute
     * @expectedException \Pants\BuildException
     */
    public function testFileIsRequired()
    {
        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        $this->chgrp
            ->setGroup(1000)
            ->execute($mockProject);
    }

    /**
     * @covers ::execute
     * @expectedException \Pants\BuildException
     */
    public function testGroupIsRequired()
    {
        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        $this->chgrp
            ->setFile($this->file)
            ->execute($mockProject);
    }


    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testGroupIsSet()
    {
        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        /** @var Properties|\PHPUnit_Framework_MockObject_MockObject $mockProperties */
        $mockProperties = $this->createMock(Properties::class);

        $mockProject->expects($this->exactly(2))
            ->method('getProperties')
            ->will($this->returnValue($mockProperties));

        $mockProperties->expects($this->at(0))
            ->method('filter')
            ->with(1000)
            ->will($this->returnArgument(0));

        $mockProperties->expects($this->at(1))
            ->method('filter')
            ->with($this->file)
            ->will($this->returnArgument(0));

        $this->chgrp
            ->setFile($this->file)
            ->setGroup(1000)
            ->execute($mockProject);

        $this->assertEquals(1000, filegroup($this->file));
    }

}
