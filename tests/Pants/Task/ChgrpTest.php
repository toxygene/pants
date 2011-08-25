<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\Task\Chgrp,
    PHPUnit_Framework_TestCase as TestCase,
    Pile\FileSystem;

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
        $fileSystem = $this->getMock(
            "Pile\FileSystem",
            array(),
            array(),
            '',
            false
        );

        $fileSystem->expects($this->once())
                   ->method("chgrp")
                   ->with("one", "two")
                   ->will($this->returnValue($file));

        $this->_chown
             ->setFileSystem($fileSystem)
             ->setFile("one")
             ->setGroup("two")
             ->execute();
    }

}
