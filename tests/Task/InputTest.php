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

use Pants\Project;
use Pants\Property\Properties;
use Pants\Task\Input;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Pants\Task\Input
 */
class InputTest extends TestCase
{

    /**
     * Input task
     *
     * @var Input
     */
    protected $task;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->task = new Input();
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
     * @covers ::getDefaultValue
     * @covers ::setDefaultValue
     */
    public function testDefaultValueCanBeSet()
    {
        $this->task
            ->setDefaultValue('test');
            
        $this->assertEquals('test', $this->task->getDefaultValue());
    }
    
    /**
     * @covers ::getMessage
     * @covers ::setMessage
     */
    public function testMessageCanBeSet()
    {
        $this->task
            ->setMessage('test');
            
        $this->assertEquals('test', $this->task->getMessage());
    }
    
    /**
     * @covers ::getPromptCharacter
     * @covers ::setPromptCharacter
     */
    public function testPromptCharacterCanBeSet()
    {
        $this->task
            ->setPromptCharacter('test');
            
        $this->assertEquals('test', $this->task->getPromptCharacter());
    }
    
    /**
     * @covers ::getPropertyName
     * @covers ::setPropertyName
     */
    public function testPropertyNameCanBeSet()
    {
        $this->task
            ->setPropertyName('test');
            
        $this->assertEquals('test', $this->task->getPropertyName());
    }
    
    /**
     * @covers ::getValidArgs
     * @covers ::setValidArgs
     */
    public function testValidArgsCanBeSet()
    {
        $this->task
            ->setValidArgs(array('one', 'two'));
            
        $this->assertEquals(array('one', 'two'), $this->task->getValidArgs());
    }
    
    /**
     * @covers ::execute
     * @expectedException \Pants\BuildException
     */
    public function testPropertyNameIsRequired()
    {
        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        $this->task
            ->execute($mockProject);
    }
    
    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testMessageIsOutput()
    {
        $message = 'message';
        
//        $this->properties
//            ->expects($this->at(0))
//            ->method('filter')
//            ->with($message)
//            ->will($this->returnArgument(0));
//
//        $this->properties
//            ->expects($this->at(1))
//            ->method('filter')
//            ->with('?')
//            ->will($this->returnArgument(0));

        $input = fopen('php://memory', 'a+');
        fwrite($input, PHP_EOL);
        fseek($input, 0);
        
        $output = fopen('php://memory', 'a+');

        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        /** @var Properties|\PHPUnit_Framework_MockObject_MockObject $mockProperties */
        $mockProperties = $this->createMock(Properties::class);

        $mockProject->expects($this->exactly(3))
            ->method('getProperties')
            ->will($this->returnValue($mockProperties));

        $mockProperties->expects($this->any())
            ->method('filter')
            ->with($this->anything())
            ->will($this->returnArgument(0));

        $this->task
            ->setInputStream($input)
            ->setOutputStream($output)
            ->setPropertyName('test')
            ->setMessage($message)
            ->execute($mockProject);

        fseek($output, 0);
        $this->assertEquals('message? ', stream_get_contents($output));
        
        fclose($input);
        fclose($output);
    }
    
    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testPromptCharacterIsOutput()
    {
        $promptCharacter = ':';
        
        $input = fopen('php://memory', 'a+');
        fwrite($input, PHP_EOL);
        fseek($input, 0);
        
        $output = fopen('php://memory', 'a+');

        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        /** @var Properties|\PHPUnit_Framework_MockObject_MockObject $mockProperties */
        $mockProperties = $this->createMock(Properties::class);

        $mockProject->expects($this->exactly(2))
            ->method('getProperties')
            ->will($this->returnValue($mockProperties));

        $mockProperties->expects($this->any())
            ->method('filter')
            ->with($this->anything())
            ->will($this->returnArgument(0));

        $this->task
            ->setInputStream($input)
            ->setOutputStream($output)
            ->setPropertyName('test')
            ->setPromptCharacter($promptCharacter)
            ->execute($mockProject);
        
        fseek($output, 0);
        $this->assertEquals(': ', stream_get_contents($output));
    }
    
    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testDefaultValueIsUsedWhenThereInNoInput()
    {
        $defaultValue = 'test';

        $input = fopen('php://memory', 'a+');
        fwrite($input, PHP_EOL);
        fseek($input, 0);
        
        $output = fopen('php://memory', 'a+');

        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        /** @var Properties|\PHPUnit_Framework_MockObject_MockObject $mockProperties */
        $mockProperties = $this->createMock(Properties::class);

        $mockProject->expects($this->exactly(3))
            ->method('getProperties')
            ->will($this->returnValue($mockProperties));

        $mockProperties->expects($this->any())
            ->method('filter')
            ->with($this->anything())
            ->will($this->returnArgument(0));

        $mockProperties->expects($this->once())
            ->method('__set')
            ->with('test', $defaultValue);

        $this->task
            ->setInputStream($input)
            ->setOutputStream($output)
            ->setDefaultValue($defaultValue)
            ->setPropertyName('test');

        $this->task
            ->execute($mockProject);
    }
    
    /**
     * @covers ::__construct
     * @covers ::execute
     */
    public function testValidArgsAreOutput()
    {
        $input = fopen('php://memory', 'a+');
        fwrite($input, 'one' . PHP_EOL);
        fseek($input, 0);
        
        $output = fopen('php://memory', 'a+');

        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        /** @var Properties|\PHPUnit_Framework_MockObject_MockObject $mockProperties */
        $mockProperties = $this->createMock(Properties::class);

        $mockProject->expects($this->exactly(4))
            ->method('getProperties')
            ->will($this->returnValue($mockProperties));

        $mockProperties->expects($this->any())
            ->method('filter')
            ->with($this->anything())
            ->will($this->returnArgument(0));
            
        $this->task
            ->setInputStream($input)
            ->setOutputStream($output)
            ->setPropertyName('test')
            ->setValidArgs(array('one', 'two'))
            ->execute($mockProject);

        fseek($output, 0);
        $this->assertContains('[one/two]', stream_get_contents($output));
    }
    
    /**
     * @covers ::execute
     * @expectedException \Pants\BuildException
     */
    public function testExceptionIsThrownWhenInvalidArgumentIsUsed()
    {
        $input = fopen('php://memory', 'a+');
        fwrite($input, 'test' . PHP_EOL);
        fseek($input, 0);
        
        $output = fopen('php://memory', 'a+');

        /** @var Project|\PHPUnit_Framework_MockObject_MockObject $mockProject */
        $mockProject = $this->createMock(Project::class);

        /** @var Properties|\PHPUnit_Framework_MockObject_MockObject $mockProperties */
        $mockProperties = $this->createMock(Properties::class);

        $mockProject->expects($this->exactly(3))
            ->method('getProperties')
            ->will($this->returnValue($mockProperties));

        $mockProperties->expects($this->any())
            ->method('filter')
            ->with($this->anything())
            ->will($this->returnArgument(0));

        $this->task
            ->setInputStream($input)
            ->setOutputStream($output)
            ->setPropertyName('test')
            ->setValidArgs(array('one', 'two'))
            ->execute($mockProject);
    }

}
