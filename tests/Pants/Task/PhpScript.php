<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\Task\PhpScript,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class PhpScriptTest extends TestCase
{

    /**
     * PhpScript task
     * @var PhpScript
     */
    protected $_task;

    /**
     * Setup the test case
     */
    public function setUp()
    {
        $this->_task = new PhpScript();
    }

}
