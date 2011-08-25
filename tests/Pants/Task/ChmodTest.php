<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\Task\Chmod,
    PHPUnit_Framework_TestCase as TestCase,
    Pile\FileSystem;

/**
 *
 */
class ChmodTest extends TestCase
{

    /**
     * Chmod task
     * @var Chmod
     */
    protected $_chmod;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->_chmod = new Chmod();
    }

    public function testPermissionsIsSetOnTheFileObject()
    {
        $fileSystem = $this->getMock(
            "Pile\FileSystem",
            array(),
            array(),
            '',
            false
        );

        $fileSystem->expects($this->once())
                   ->method("chmod")
                   ->with("file", "0755")
                   ->will($this->returnValue($fileSystem));

        $this->_chmod
             ->setFileSystem($fileSystem)
             ->setFile("file")
             ->setMode("0755")
             ->execute();
    }

}
