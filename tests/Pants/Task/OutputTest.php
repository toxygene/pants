<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\Task\Output,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class OutputTest extends TestCase
{

    /**
     * Output task
     * @var Output
     */
    protected $_task;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->_task = new Output();
    }

    public function testMessageCanBeSet()
    {
        $this->_task->setMessage("one");
        $this->assertEquals("one", $this->_task->getMessage());
    }

    public function testMessageIsPrintedOnExecute()
    {
        $this->_task->setMessage("one");

        ob_start();
        $this->_task->execute();
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals("one", $output);
    }

}
