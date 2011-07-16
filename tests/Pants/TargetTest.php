<?php
/**
 *
 */

namespace PantsTest;

use Pants\Target,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class TargetTest extends TestCase
{

    /**
     * Target
     * @var Target
     */
    protected $_target;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->_target = new Target;
    }

    public function testNameCanBeSet()
    {
        $this->_target->setName("test");

        $this->assertEquals("test", $this->_target->getName());
    }

    public function testDescriptionCanBeSet()
    {
        $this->_target->setDescription("test");

        $this->assertEquals("test", $this->_target->getDescription());
    }

    public function testTasksCanBeRetrieved()
    {
        $this->assertInstanceOf("\Pants\Tasks", $this->_target->getTasks());
    }

    public function testTasksAreExecutedOnTargetExecute()
    {
        $task = $this->getMock("\Pants\Task");

        $task->expects($this->exactly(2))
             ->method("execute")
             ->will($this->returnValue($task));

        $this->_target
             ->getTasks()
             ->add($task)
             ->add($task);

        $this->_target
             ->execute();
    }

}
