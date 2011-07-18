<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\File,
    Pants\File\RuntimeException,
    Pants\Task\Chown,
    PHPUnit_Framework_TestCase as TestCase;

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
        $file = $this->getMock(
            "Pants\File",
            array(),
            array(),
            '',
            false
        );

        $file->expects($this->once())
             ->method("setOwner")
             ->with("test")
             ->will($this->returnValue($file));

        $this->_chown
             ->setFile($file)
             ->setOwner("test")
             ->execute();
    }

}
