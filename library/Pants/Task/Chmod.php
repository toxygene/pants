<?php
/**
 *
 */

namespace Pants\Task;

use Pants\Task\AbstractFileTask;

/**
 *
 */
class Chmod extends AbstractFileTask
{

    /**
     * Target file
     * @var string
     */
    protected $_file;

    /**
     * Mode to set
     * @var string
     */
    protected $_mode;

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
     * Get the mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->_mode;
    }

    /**
     * Execute the task
     *
     * @return Chmod
     */
    public function execute()
    {
        $this->getFileSystem()
             ->chmod($this->getFile(), $this->getMode());

        return $this;
    }

    /**
     * Set the target file
     *
     * @param string $file
     * @return Chmod
     */
    public function setFile($file)
    {
        $this->_file = $file;
        return $this;
    }

    /**
     * Set the mode
     *
     * @param string $mode
     * @return Chmod
     */
    public function setMode($mode)
    {
        $this->_mode = $mode;
        return $this;
    }

}
