<?php
/**
 *
 */

namespace Pants\Task;

use Pants\File,
    Pants\FileSets,
    Pants\Task\AbstractTask,
    Pants\FileSetTask;

/**
 *
 */
class Chgrp extends AbstractTask implements FileSetTask
{

    /**
     * The target file
     * @var File
     */
    protected $_file;

    /**
     * Filesets
     * @var FileSets
     */
    protected $_filesets;

    /**
     * Group to set
     * @var string
     */
    protected $_group;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_filesets = new FileSets();
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
     * Get the target filesets
     *
     * @return FileSets
     */
    public function getFileSets()
    {
        return $this->_filesets;
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
        $this->getFile()
             ->setGroup($this->getGroup());

        foreach ($this->getFileSets() as $fileSet) {
            foreach ($fileSet as $file) {
                $file->setGroup($this->getGroup());
            }
        }

        return $this;
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
