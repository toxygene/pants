<?php
/**
 *
 */

namespace Pants\Task;

use Pants\Task\AbstractFileTask;

/**
 *
 */
class Chgrp extends AbstractFileTask
{

    /**
     * Target file
     * @var string
     */
    protected $_file;

    /**
     * Group to set
     * @var string
     */
    protected $_group;

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
     * Get the group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->_group;
    }

    /**
     * Execute the task
     *
     * @return Chgrp
     */
    public function execute()
    {
        $this->getFileSystem()
             ->chgrp($this->getFile(), $this->getGroup());

        return $this;
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

    /**
     * Set the group
     *
     * @param string $group
     * @return Chgrp
     */
    public function setGroup($group)
    {
        $this->_group = $group;
        return $this;
    }

}
