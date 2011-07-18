<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\File,
    Pants\File\RuntimeException,
    Pants\Task\Copy,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class CopyTest extends TestCase
{

    /**
     * Copy task
     * @var Copy
     */
    protected $_Copy;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->_Copy = new Copy();
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
             ->method("Copy")
             ->with("destination");

        $this->_Copy
             ->setFile($file)
             ->setDestination("destination")
             ->execute();
    }

}
