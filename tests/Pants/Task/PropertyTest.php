<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\Project,
    Pants\Task\Property,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class PropertyTest extends TestCase
{

    /**
     * Properties task
     * @var Properties
     */
    protected $_task;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->_task = new Property();
        $this->_task->setProject(new Project());
    }

    public function testNameCanBeSet()
    {
        $this->_task->setName("one");
        $this->assertEquals("one", $this->_task->getName());
    }

    public function testValueCanBeSet()
    {
        $this->_task->setValue("one");
        $this->assertEquals("one", $this->_task->getValue());
    }

    public function testPropertiesAreSetOnTheProjectOnExecute()
    {
        $this->_task
             ->setName("one")
             ->setValue("two")
             ->execute();

        $this->assertEquals("two", $this->_task->getProject()->getProperties()->one);
    }

}
