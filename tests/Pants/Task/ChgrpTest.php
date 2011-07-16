<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\File,
    Pants\File\RuntimeException,
    Pants\Task\Chgrp,
    PHPUnit_Framework_TestCase as TestCase;

/**
 *
 */
class ChgrpTest extends TestCase
{

    /**
     * Chgrp task
     * @var Chgrp
     */
    protected $_chown;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->_chown = new Chgrp();
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
             ->method("setGroup")
             ->with("test")
             ->will($this->returnValue($file));

        $this->_chown
             ->setFile($file)
             ->setGroup("test")
             ->execute();
    }

}
