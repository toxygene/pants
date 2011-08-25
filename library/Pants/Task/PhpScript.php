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
     * Target file
     * @var string
     */
    protected $_file;

    /**
     * Execute the task
     *
     * @return PhpScript
     */
    public function execute()
    {
        require $this->getFile();

        return $this;
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
     * Set the target file
     *
     * @param string $file
     * @return Chgrp
     */
    public function setFile($file)
    {
        $this->_file = $file;
        return $this;
    }

}
