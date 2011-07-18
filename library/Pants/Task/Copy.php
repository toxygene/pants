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
class Copy extends AbstractTask
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
     * @return Copy
     */
    public function execute()
    {
        $this->getFile()
             ->copy($this->getDestination());

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
     * @param string|File $file
     * @return Copy
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
