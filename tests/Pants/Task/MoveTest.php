<?php
/**
 *
 */

namespace PantsTest\Task;

use Pants\File,
    Pants\File\RuntimeException,
    Pants\Task\Move,
    PHPUnit_Framework_TestCase as TestCase;

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
        $file = $this->getMock(
            "Pants\File",
            array(),
            array(),
            '',
            false
        );

        $file->expects($this->once())
             ->method("move")
             ->with("destination");

        $this->_move
             ->setFile($file)
             ->setDestination("destination")
             ->execute();
    }

}
