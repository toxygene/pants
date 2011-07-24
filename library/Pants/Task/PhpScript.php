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
class PhpScript extends AbstractTask
{

    /**
     * The target file
     * @var File
     */
    protected $_file;

    /**
     * Execute the task
     */
    public function execute()
    {
        require $this->getFile()
                     ->getRealPath();
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
     * Set the target file
     *
     * @param string|File $file
     * @return Chgrp
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
