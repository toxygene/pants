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
 *     * The name of its contributor may not be used to endorse or promote
 *       products derived from this software without specific prior written
 *       permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL JUSTIN HENDRICKSON BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace PantsTest;

use Pants\Properties,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class PropertiesTest extends TestCase
{

    /**
     * Properties
     * @var Properties
     */
    protected $_properties;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->_properties = new Properties();
    }

    public function testGettingAndSettingProperties()
    {
        $this->_properties->one = "two";
        $this->_properties->three = "four";

        $this->assertEquals("two", $this->_properties->one);
        $this->assertEquals("four", $this->_properties->three);
    }

    public function testUnsettingANonexistantPropertyThrowsAnException()
    {
        $this->setExpectedException("InvalidArgumentException");

        unset($this->_properties->one);
    }

    public function testGettingANonexistantPropertyThrowsAnException()
    {
        $this->setExpectedException("InvalidArgumentException");

        $this->_properties->one;
    }

    public function testExistanceOfPropertiesCanBeChecked()
    {
        $this->_properties->one = "two";

        $this->assertTrue(isset($this->_properties->one));
        $this->assertFalse(isset($this->_properties->two));
    }

    public function testFilteringReplacesPropertiesWithTheirValues()
    {
        $this->_properties->one = "two";
        $this->_properties->three = '${one}';

        $this->assertEquals(
            "test two test two test",
            $this->_properties->filter('test ${one} test ${three} test')
        );
    }

    public function testDetectedPropertyCyclesThrowAnException()
    {
        $this->setExpectedException("Pants\Properties\PropertyNameCycleException");

        $this->_properties->one = '${two}';
        $this->_properties->two = '${one}';

        $this->_properties->filter('${one}');
    }

}
