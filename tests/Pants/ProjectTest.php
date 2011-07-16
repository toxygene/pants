<?php
/**
 *
 */

namespace PantsTest;

use Pants\Project,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class ProjectTest extends TestCase
{

    /**
     * Project
     * @var Project
     */
    protected $_project;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->_project = new Project();
    }

    public function testDefaultCanBeSet()
    {
        $this->_project->setDefault("test");

        $this->assertEquals("test", $this->_project->getDefault());
    }

    public function testPropertiesCanBeRetrieved()
    {
        $this->assertInstanceOf("Pants\Properties", $this->_project->getProperties());
    }

    public function testTasksAreAutomaticallyExecuted()
    {
        $task = $this->getMock("Pants\Task");

        $task->expects($this->once())
             ->method("setProject")
             ->with($this->_project)
             ->will($this->returnValue($task));

        $task->expects($this->once())
             ->method("execute")
             ->will($this->returnValue($task));

        $this->_project->addTask($task);
    }

}
