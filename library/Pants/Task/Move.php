<?php
/**
 *
 */

namespace Pants\Task;

use Pants\File,
    Pants\Task\AbstractTask;

/**
 *
 */
class Move extends AbstractTask
{

    /**
     * The target file
     * @var File
     */
    protected $_file;

    /**
     * The destination file
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
     * @return File
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
        $this->getFile()
             ->move($this->getDestination());

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
     * @param string|File $file
     * @return Move
     */
    public function setFile($file)
    {
        if (!$file instanceof File) {
            $file = new File($file);
        }

        $this->_file = $file;
        return $this;
    }

}
