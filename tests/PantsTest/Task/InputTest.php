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

namespace PantsTest\Task;

use Pants\Task\Input;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
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
     * Setup the test case
     */
    public function setUp()
    {
        $this->task = new Input($this->getMock('\Pants\Property\Properties'));
    }
    
    /**
     * Tear down the test case
     */
    public function tearDown()
    {
        unset($this->task);
    }
    
    /**
     * @covers Pants\Task\Input::getDefaultValue
     * @covers Pants\Task\Input::setDefaultValue
     */
    public function testDefaultValueCanBeSet()
    {
        $this->task
            ->setDefaultValue('test');
            
        $this->assertEquals('test', $this->task->getDefaultValue());
    }
    
    /**
     * @covers Pants\Task\Input::getInputStream
     * @covers Pants\Task\Input::setInputStream
     */
    public function testInputStreamCanBeSet()
    {
        $input = fopen('php://memory', 'a+');
        
        $this->task
            ->setInputStream($input);
            
        $this->assertEquals($input, $this->task->getInputStream());
        
        fclose($input);
    }
    
    /**
     * @covers Pants\Task\Input::getOutputStream
     * @covers Pants\Task\Input::setOutputStream
     */
    public function testOutputStreamCanBeSet()
    {
        $output = fopen('php://memory', 'a+');
        
        $this->task
            ->setOutputStream($output);
            
        $this->assertEquals($output, $this->task->getOutputStream());
        
        fclose($output);
    }
    
    /**
     * @covers Pants\Task\Input::getMessage
     * @covers Pants\Task\Input::setMessage
     */
    public function testMessageCanBeSet()
    {
        $this->task
            ->setMessage('test');
            
        $this->assertEquals('test', $this->task->getMessage());
    }
    
    /**
     * @covers Pants\Task\Input::getPromptCharacter
     * @covers Pants\Task\Input::setPromptCharacter
     */
    public function testPromptCharacterCanBeSet()
    {
        $this->task
            ->setPromptCharacter('test');
            
        $this->assertEquals('test', $this->task->getPromptCharacter());
    }
    
    /**
     * @covers Pants\Task\Input::getPropertyName
     * @covers Pants\Task\Input::setPropertyName
     */
    public function testPropertyNameCanBeSet()
    {
        $this->task
            ->setPropertyName('test');
            
        $this->assertEquals('test', $this->task->getPropertyName());
    }
    
    /**
     * @covers Pants\Task\Input::getValidArgs
     * @covers Pants\Task\Input::setValidArgs
     */
    public function testValidArgsCanBeSet()
    {
        $this->task
            ->setValidArgs(array('one', 'two'));
            
        $this->assertEquals(array('one', 'two'), $this->task->getValidArgs());
    }
    
    /**
     * @covers Pants\Task\Input::execute
     */
    public function testPropertyNameIsRequired()
    {
        $this->setExpectedException('Pants\BuildException');
        
        $this->task
            ->execute();
    }
    
    /**
     * @covers Pants\Task\Input::execute
     */
    public function testMessageIsOutput()
    {
        $message = 'message';
        
        $this->task
            ->getProperties()
            ->expects($this->at(0))
            ->method('filter')
            ->with($message)
            ->will($this->returnArgument(0));

        $this->task
            ->getProperties()
            ->expects($this->at(1))
            ->method('filter')
            ->with('?')
            ->will($this->returnArgument(0));
            
        $input = fopen('php://memory', 'a+');
        fwrite($input, PHP_EOL);
        fseek($input, 0);
        
        $output = fopen('php://memory', 'a+');

        $this->task
            ->setInputStream($input)
            ->setOutputStream($output)
            ->setPropertyName('test')
            ->setMessage($message);

        $this->task->execute();

        fseek($output, 0);
        $this->assertEquals('message? ', stream_get_contents($output));
        
        fclose($input);
        fclose($output);
    }
    
    /**
     * @covers Pants\Task\Input::execute
     */
    public function testPromptCharacterIsOutput()
    {
        $promptCharacter = ':';
        
        $this->task
            ->getProperties()
            ->expects($this->at(0))
            ->method('filter')
            ->with(':')
            ->will($this->returnArgument(0));
        
        $input = fopen('php://memory', 'a+');
        fwrite($input, PHP_EOL);
        fseek($input, 0);
        
        $output = fopen('php://memory', 'a+');

        $this->task
            ->setInputStream($input)
            ->setOutputStream($output)
            ->setPropertyName('test')
            ->setPromptCharacter($promptCharacter);

        $this->task->execute();
        
        fseek($output, 0);
        $this->assertEquals(': ', stream_get_contents($output));
    }
    
    /**
     * @covers Pants\Task\Input::execute
     */
    public function testDefaultValueIsUsedWhenThereInNoInput()
    {
        $defaultValue = 'test';

        $this->task
            ->getProperties()
            ->expects($this->at(0))
            ->method('filter')
            ->with('?')
            ->will($this->returnArgument(0));

        $this->task
            ->getProperties()
            ->expects($this->at(1))
            ->method('filter')
            ->with($defaultValue)
            ->will($this->returnArgument(0));
            
        $this->task
            ->getProperties()
            ->expects($this->once())
            ->method('__get')
            ->with('test')
            ->will($this->returnValue($defaultValue));
    
        $input = fopen('php://memory', 'a+');
        fwrite($input, PHP_EOL);
        fseek($input, 0);
        
        $output = fopen('php://memory', 'a+');

        $this->task
            ->setInputStream($input)
            ->setOutputStream($output)
            ->setDefaultValue($defaultValue)
            ->setPropertyName('test');

        $this->task
            ->execute();

        $this->assertEquals('test', $this->task->getProperties()->test);
    }
    
    /**
     * @covers Pants\Task\Input::execute
     */
    public function testValidArgsAreOutput()
    {
        $input = fopen('php://memory', 'a+');
        fwrite($input, 'one' . PHP_EOL);
        fseek($input, 0);
        
        $output = fopen('php://memory', 'a+');

        $this->task
            ->getProperties()
            ->expects($this->at(0))
            ->method('filter')
            ->with('one')
            ->will($this->returnValue('one'));
            
        $this->task
            ->getProperties()
            ->expects($this->at(1))
            ->method('filter')
            ->with('two')
            ->will($this->returnValue('two'));
            
        $this->task
            ->setInputStream($input)
            ->setOutputStream($output)
            ->setPropertyName('test')
            ->setValidArgs(array('one', 'two'));

        $this->task
            ->execute();

        fseek($output, 0);
        $this->assertContains('[one/two]', stream_get_contents($output));
    }
    
    /**
     * @covers Pants\Task\Input::execute
     */
    public function testExceptionIsThrownWhenInvalidArgumentIsUsed()
    {
        $this->setExpectedException('Pants\BuildException');
        
        $input = fopen('php://memory', 'a+');
        fwrite($input, 'test' . PHP_EOL);
        fseek($input, 0);
        
        $output = fopen('php://memory', 'a+');

        $this->task
            ->setInputStream($input)
            ->setOutputStream($output)
            ->setPropertyName('test')
            ->setValidArgs(array('one', 'two'));

        $this->task->execute();
    }

}
