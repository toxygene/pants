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

namespace PantsTest\Target;

use Pants\Target\Targets;
use PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class TargetsTest extends TestCase
{

    /**
     * Targets
     *
     * @var Targets
     */
    protected $targets;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->targets = new Targets();
    }

    /**
     * @covers Pants\Target\Targets::__get
     * @covers Pants\Target\Targets::add
     */
    public function testTargetsCanBeAdded()
    {
        $target = $this->getMockBuilder('\Pants\Target\Target')
            ->disableOriginalConstructor()
            ->getMock();

        $target->expects($this->once())
              ->method('getName')
              ->will($this->returnValue('test'));

        $this->targets
            ->add($target);
        
        $this->assertSame($target, $this->targets->test);
    }
    
    /**
     * @covers Pants\Target\Targets::__get
     */
    public function testGettingANonExistentTargetThrowsAnException()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        $this->targets->test;
    }

    /**
     * @covers Pants\Target\Targets::__isset
     */
    public function testTargetsCanBeCheckedForExistance()
    {
        $target = $this->getMockBuilder('\Pants\Target\Target')
            ->disableOriginalConstructor()
            ->getMock();

        $target->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('test'));

        $this->targets
            ->add($target);

        $this->assertTrue(isset($this->targets->test));
        $this->assertFalse(isset($this->targets->asdf));
    }

    /**
     * @covers Pants\Target\Targets::__unset
     */
    public function testTargetsCanBeRemoved()
    {
        $target = $this->getMockBuilder('\Pants\Target\Target')
            ->disableOriginalConstructor()
            ->getMock();

        $target->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('test'));

        $this->targets
            ->add($target);

        $this->assertTrue(isset($this->targets->test));

        unset($this->targets->test);

        $this->assertFalse(isset($this->targets->test));
    }

    /**
     * @covers Pants\Target\Targets::__unset
     */
    public function testRemovingATargetThatDoesNotExistThrowsAnException()
    {
        $this->setExpectedException('InvalidArgumentException');
        
        unset($this->targets->test);
    }

    /**
     * @covers Pants\Target\Targets::add
     */
    public function testCannotAddATargetWithTheSameNameAsAnExistingTarget()
    {
        $this->setExpectedException('InvalidArgumentException');

        $target = $this->getMockBuilder('\Pants\Target\Target')
            ->disableOriginalConstructor()
            ->getMock();

        $target->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test'));

        $this->targets
            ->add($target)
            ->add($target);
    }
    
    /**
     * @covers Pants\Target\Targets::getDescriptions
     */
    public function testDescriptionsCanBeRetrieved()
    {
        $target = $this->getMockBuilder('\Pants\Target\Target')
            ->disableOriginalConstructor()
            ->getMock();

        $target->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('test'));

        $target->expects($this->once())
            ->method('getDescription')
            ->will($this->returnValue('test'));

        $this->targets
            ->add($target);
            
        $this->assertEquals(array('test' => 'test'), $this->targets->getDescriptions());
    }
    
    /**
     * @covers Pants\Target\Targets::getDescriptions
     */
    public function testHiddenTargetsAreNotAddedToDescriptions()
    {
        $target = $this->getMockBuilder('\Pants\Target\Target')
            ->disableOriginalConstructor()
            ->getMock();

        $target->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('test'));

        $target->expects($this->once())
            ->method('getHidden')
            ->will($this->returnValue(true));

        $target->expects($this->never())
            ->method('getDescription');

        $this->targets
            ->add($target);
            
        $this->assertEquals(array(), $this->targets->getDescriptions());
    }

}
