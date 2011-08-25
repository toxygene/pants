<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\Task\Chown,
    PHPUnit_Framework_TestCase as TestCase,
    Pile\FileSystem;

/**
 *
 */
class ChownTest extends TestCase
{

    /**
     * Chown task
     * @var Chown
     */
    protected $_chown;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->_chown = new Chown();
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
                   ->method("chown")
                   ->with("file", "owner")
                   ->will($this->returnValue($fileSystem));

        $this->_chown
             ->setFileSystem($fileSystem)
             ->setFile("file")
             ->setOwner("owner")
             ->execute();
    }

}
