<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\File,
    Pants\File\RuntimeException,
    Pants\Task\Chmod,
    PHPUnit_Framework_TestCase as TestCase;

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
        $file = $this->getMock(
            "Pants\File",
            array(),
            array(),
            '',
            false
        );

        $file->expects($this->once())
             ->method("setPermission")
             ->with("0755")
             ->will($this->returnValue($file));

        $this->_chmod
             ->setFile($file)
             ->setMode("0755")
             ->execute();
    }

}
