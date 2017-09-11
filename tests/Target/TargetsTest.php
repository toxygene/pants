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

namespace Pants\Test\Target;

use Pants\Target\Target;
use Pants\Target\TargetInterface;
use Pants\Target\Targets;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Pants\Target\Targets
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
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->targets = new Targets();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->targets);
    }

    /**
     * @covers ::__get
     * @covers ::add
     */
    public function testTargetsCanBeAdded()
    {
        /** @var Target|\PHPUnit_Framework_MockObject_MockObject $target */
        $target = $this->getMockBuilder(Target::class)
            ->disableOriginalConstructor()
            ->getMock();

        $target->expects($this->once())
              ->method('getName')
              ->will($this->returnValue('test'));

        $this->targets
            ->add($target);
        
        $this->assertSame($target, $this->targets->get('test'));
    }
    
    /**
     * @covers ::__get
     * @expectedException \InvalidArgumentException
     */
    public function testGettingANonExistentTargetThrowsAnException()
    {
        $this->targets->get('test');
    }

    /**
     * @covers ::__isset
     */
    public function testTargetsCanBeCheckedForExistance()
    {
        /** @var Target|\PHPUnit_Framework_MockObject_MockObject $target */
        $target = $this->getMockBuilder(Target::class)
            ->disableOriginalConstructor()
            ->getMock();

        $target->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('test'));

        $this->targets
            ->add($target);

        $this->assertTrue($this->targets->exists('test'));
        $this->assertFalse($this->targets->exists('asdf'));
    }

    /**
     * @covers ::__unset
     */
    public function testTargetsCanBeRemoved()
    {
        /** @var Target|\PHPUnit_Framework_MockObject_MockObject $target */
        $target = $this->createMock(Target::class);

        $target->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('test'));

        $this->targets
            ->add($target);

        $this->assertTrue($this->targets->exists('test'));

        $this->targets->remove('test');

        $this->assertFalse($this->targets->exists('test'));
    }

    /**
     * @covers ::__unset
     * @expectedException \InvalidArgumentException
     */
    public function testRemovingATargetThatDoesNotExistThrowsAnException()
    {
        $this->targets->remove('test');
    }

    /**
     * @covers ::add
     * @expectedException \InvalidArgumentException
     */
    public function testCannotAddATargetWithTheSameNameAsAnExistingTarget()
    {
        /** @var TargetInterface|\PHPUnit_Framework_MockObject_MockObject $target */
        $target = $this->createMock(TargetInterface::class);

        $target->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test'));

        $this->targets
            ->add($target)
            ->add($target);
    }
    
    /**
     * @covers ::getDescriptions
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
     * @covers ::getDescriptions
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
