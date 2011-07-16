<?php
/**
 *
 */

namespace PantsTest;

use Pants\Targets,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class TargetsTest extends TestCase
{

    /**
     * Project
     * @var \Pants\Project
     */
    protected $_project;

    /**
     * Targets
     * @var Targets
     */
    protected $_targets;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->_project = $this->getMock("\Pants\Project");

        $this->_targets = new Targets($this->_project);
    }

    public function testAddingATargetSetsTheProject()
    {
        $target = $this->getMock("\Pants\Target");

        $target->expects($this->once())
               ->method("setProject")
               ->with($this->equalTo($this->_project))
               ->will($this->returnValue($target));

        $target->expects($this->exactly(2))
               ->method("getName")
               ->will($this->returnValue("test"));

        $this->_targets->add($target);
    }

    public function testTargetsCanBeCheckedForExistance()
    {
        $target = $this->getMock("\Pants\Target");

        $target->expects($this->once())
               ->method("setProject")
               ->with($this->equalTo($this->_project))
               ->will($this->returnValue($target));

        $target->expects($this->exactly(2))
               ->method("getName")
               ->will($this->returnValue("test"));

        $this->_targets->add($target);

        $this->assertTrue(isset($this->_targets->test));
        $this->assertFalse(isset($this->_targets->asdf));
    }

    public function testTargetsCanBeRemoved()
    {
        $target = $this->getMock("\Pants\Target");

        $target->expects($this->once())
               ->method("setProject")
               ->with($this->equalTo($this->_project))
               ->will($this->returnValue($target));

        $target->expects($this->exactly(2))
               ->method("getName")
               ->will($this->returnValue("test"));

        $this->_targets->add($target);

        $this->assertTrue(isset($this->_targets->test));

        unset($this->_targets->test);

        $this->assertFalse(isset($this->_targets->test));
    }

    public function testProjectCanBeRetrieved()
    {
        $this->assertSame($this->_project, $this->_targets->getProject());
    }

}
