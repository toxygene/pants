<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\Task\Copy,
    PHPUnit_Framework_TestCase as TestCase,
    Pile\FileSystem;

/**
 *
 */
class CopyTest extends TestCase
{

    /**
     * Copy task
     * @var Copy
     */
    protected $_copy;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->_copy = new Copy();
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
                   ->method("copy")
                   ->with("file", "destination")
                   ->will($this->returnValue($fileSystem));

        $this->_copy
             ->setFileSystem($fileSystem)
             ->setFile("file")
             ->setDestination("destination")
             ->execute();
    }

}
