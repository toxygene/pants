<?php
/**
 *
 */

namespace Pants\Task;

use Pants\Task\AbstractFileTask;

/**
 *
 */
class Delete extends AbstractFileTask
{

    /**
     * Target file
     * @var string
     */
    protected $_file;

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
     * @return Delete
     */
    public function execute()
    {
        $this->getFileSystem()
             ->unlink($this->getFile());

        return $this;
    }

    /**
     * Set the target file
     *
     * @param string $file
     * @return Delete
     */
    public function setFile($file)
    {
        $this->_file = $file;
        return $this;
    }

}
