<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\Task\Delete,
    PHPUnit_Framework_TestCase as TestCase,
    Pile\FileSystem;

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
        $fileSystem = $this->getMock(
            "Pile\FileSystem",
            array(),
            array(),
            '',
            false
        );

        $fileSystem->expects($this->once())
                   ->method("unlink")
                   ->with("file")
                   ->will($this->returnValue($fileSystem));

        $this->_delete
             ->setFileSystem($fileSystem)
             ->setFile("file")
             ->execute();
    }

}
