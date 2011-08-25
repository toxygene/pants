<?php
/**
 *
 */

namespace Pants\Task;

use Pants\Task\AbstractFileTask;

/**
 *
 */
class Move extends AbstractFileTask
{

    /**
     * Target file
     * @var string
     */
    protected $_file;

    /**
     * Destination file
     * @var string
     */
    protected $_destination;

    /**
     * Get the destination
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->_destination;
    }

    /**
     * Get the target file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * Execute the task
     *
     * @return Move
     */
    public function execute()
    {
        $this->getFileSystem()
             ->move($this->getFile(), $this->getDestination());

        return $this;
    }

    /**
     * Set the destination file
     *
     * @param string $destination
     * @return Move
     */
    public function setDestination($destination)
    {
        $this->_destination = $destination;
        return $this;
    }

    /**
     * Set the target file
     *
     * @param string $file
     * @return Move
     */
    public function setFile($file)
    {
        $this->_file = $file;
        return $this;
    }

}
