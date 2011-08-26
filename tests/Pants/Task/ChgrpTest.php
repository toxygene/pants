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
    protected $_chgrp;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->_chgrp = new Chgrp();
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
                   ->with("file", "group")
                   ->will($this->returnValue($fileSystem));

        $this->_chgrp
             ->setFileSystem($fileSystem)
             ->setFile("file")
             ->setGroup("group")
             ->execute();
    }

}
