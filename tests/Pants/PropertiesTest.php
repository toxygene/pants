<?php
/**
 *
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
