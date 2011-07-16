<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\File,
    Pants\File\RuntimeException,
    Pants\Task\Delete,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class DeleteTest extends TestCase
{

    /**
     * Delete task
     * @var Delete
     */
    protected $_delete;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->_delete = new Delete();
    }

    public function testOwnerIsSetOnTheFileObject()
    {
        $file = $this->getMock(
            "Pants\File",
            array(),
            array(),
            '',
            false
        );

        $file->expects($this->once())
             ->method("delete");

        $this->_delete
             ->setFile($file)
             ->execute();
    }

}
