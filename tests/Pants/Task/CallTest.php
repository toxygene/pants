<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\Project,
    Pants\Task\Call,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class CallTest extends TestCase
{

    /**
     * Call task
     * @var Call
     */
    protected $_call;

    /**
     * Mock project

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->_call = new Call();
    }

    public function testTargetCanBeSet()
    {
        $this->_call->setTarget("asdf");

        $this->assertEquals("asdf", $this->_call->getTarget());
    }

    public function testCallingAnInvalidTargetThrowsAnInvalidArgumentException()
    {
        $this->setExpectedException("\InvalidArgumentException");

        $this->_call
             ->setProject(new Project())
             ->setTarget("asdf")
             ->execute();
    }

    public function testCallingAValidTargetExecutesTheTarget()
    {
        $project = new Project();

        $mock = $this->getMock("Pants\Target");

        $mock->expects($this->any())
             ->method("getName")
             ->will($this->returnValue("asdf"));

        $mock->expects($this->once())
             ->method("execute")
             ->will($this->returnValue($mock));

        $project->getTargets()
                ->add($mock);

        $this->_call
             ->setProject($project)
             ->setTarget("asdf")
             ->execute();
    }

}
