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
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
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

namespace Pants\Test\Properties;

use Pants\Property\Properties;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Pants\Property\Properties
 */
class PropertiesTest extends TestCase
{

    /**
     * Properties
     * @var Properties
     */
    protected $properties;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->properties = new Properties();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->properties);
    }

    /**
     * @covers ::get
     * @covers ::add
     */
    public function testGettingAndSettingProperties()
    {
        $this->properties->add('one', 'two');
        $this->properties->add('three', 'four');

        $this->assertEquals('two', $this->properties->get('one'));
        $this->assertEquals('four', $this->properties->get('three'));
    }

    /**
     * @covers ::get
     * @expectedException \InvalidArgumentException
     */
    public function testGettingANonExistantPropertyThrowsAnException()
    {
        $this->properties->get('one');
    }

    /**
     * @covers ::exists
     */
    public function testExistanceOfPropertiesCanBeChecked()
    {
        $this->properties->add('one', 'two');

        $this->assertTrue($this->properties->exists('one'));
        $this->assertFalse($this->properties->exists('two'));
    }

    /**
     * @covers ::remove
     */
    public function testUnsettingAPropertyRemovesIt()
    {
        $this->properties->add('one', 'test');

        $this->assertTrue($this->properties->exists('one'));

        $this->properties->remove('one');
        
        $this->assertFalse($this->properties->exists('one'));
    }

    /**
     * @covers ::remove
     * @expectedException \InvalidArgumentException
     */
    public function testUnsettingANonexistantPropertyThrowsAnException()
    {
        $this->properties->remove('one');
    }
    
    /**
     * @covers ::filter
     */
    public function testFilteringReplacesPropertiesWithTheirValues()
    {
        $this->properties->add('one', 'two');
        $this->properties->add('three', '${one}');

        $this->assertEquals(
            "test two test two test",
            $this->properties->filter('test ${one} test ${three} test')
        );
    }
    
    /**
     * @covers ::filter
     * @expectedException \Pants\Property\PropertyNameCycleException
     */
    public function testDetectedPropertyCyclesThrowAnException()
    {
        $this->properties->add('one', '${two}');
        $this->properties->add('two', '${one}');

        $this->properties->filter('${one}');
    }

}
