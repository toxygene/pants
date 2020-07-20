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

namespace Pants\Test\Task;

use Pants\Task\TaskInterface;
use Pants\Task\Tasks;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the tasks container
 *
 * @coversDefaultClass \Pants\Task\Tasks
 */
class TasksTest extends TestCase
{

    /**
     * Tasks
     * @var Tasks
     */
    protected $tasks;
    
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->tasks = new Tasks();
    }
    
    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();
        
        unset($this->tasks);
    }
    
    /**
     * @covers ::add
     * @covers ::getIterator
     */
    public function testTasksCanBeAdded()
    {
        /** @var TaskInterface|\PHPUnit_Framework_MockObject_MockObject $mockTask */
        $mockTask = $this->createMock(TaskInterface::class);
        
        $this->tasks
            ->add($mockTask);

        $this->assertContains($mockTask, $this->tasks->toArray());
    }

}