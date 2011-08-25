<?php
/**
 *
 */

namespace Pants\Task;

use Pants\Task\AbstractFileTask;

/**
 *
 */
class Chown extends AbstractFileTask
{

    /**
     * Target file
     * @var string
     */
    protected $_file;

    /**
     * Owner to set
     * @var string
     */
    protected $_owner;

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
     * Get the owner
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->_owner;
    }

    /**
     * Execute the task
     *
     * @return Chown
     */
    public function execute()
    {
        $this->getFileSystem()
             ->chmod($this->getFile(), $this->getOwner());

        return $this;
    }

    /**
     * Set the target file
     *
     * @param string $file
     * @return Chown
     */
    public function setFile($file)
    {
        $this->_file = $file;
        return $this;
    }

    /**
     * Set the owner
     *
     * @param string $owner
     * @return Chown
     */
    public function setOwner($owner)
    {
        $this->_owner = $owner;
        return $this;
    }

}
