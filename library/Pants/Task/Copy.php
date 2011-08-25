<?php
/**
 *
 */

namespace Pants\Task;

use Pants\Task\AbstractFileTask;

/**
 *
 */
class Copy extends AbstractFileTask
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
     * @return Copy
     */
    public function execute()
    {
        $this->getFileSystem()
             ->copy($this->getFile(), $this->getDestination());

        return $this;
    }

    /**
     * Set the destination file
     *
     * @param string $destination
     * @return Copy
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
     * @return Copy
     */
    public function setFile($file)
    {
        $this->_file = $file;
        return $this;
    }

}
