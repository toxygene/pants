<?php
/**
 * Pants
 *
 * Copyright (c) 2014-2017, Justin Hendrickson
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

namespace Pants\Test\Task;

use Pants\Task\Symlink;

/**
 * Unit tests for the symlink task
 * 
 * @coversDefaultClass \Pants\Task\Symlink
 */
class SymlinkTest extends TaskTestCase
{

    /**
     * Symlink task
     *
     * @var Symlink
     */
    protected $task;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->task = new Symlink();
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();
        
        unset($this->task);
    }
    
    /**
     * @covers ::execute
     * @expectedException \Pants\Task\BuildException
     */
    public function testMissingLinkThrowsException()
    {
        $this->task
            ->execute($this->mockContext);
    }
    
    /**
     * @covers ::execute
     * @expectedException \Pants\Task\BuildException
     */
    public function testMissingTargetThrowsException()
    {
        $this->task
            ->setLink('test')
            ->execute($this->mockContext);
    }
    
    /**
     * @covers ::execute
     */
    public function testExecuteCreatesASymlink()
    {
        $this->markTestSkipped('PHP streams do not support symlinks, so this has to be done on the filesystem');
    }

}
