<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\Task\Move,
    PHPUnit_Framework_TestCase as TestCase,
    Pile\FileSystem;

/**
 *
 */
class MoveTest extends TestCase
{

    /**
     * Move task
     * @var Move
     */
    protected $_move;

    /**
     * Setup the test
     */
    public function setUp()
    {
        $this->_move = new Move();
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
                   ->method("move")
                   ->with("file", "destination")
                   ->will($this->returnValue($fileSystem));

        $this->_move
             ->setFileSystem($fileSystem)
             ->setFile("file")
             ->setDestination("destination")
             ->execute();
    }

}
