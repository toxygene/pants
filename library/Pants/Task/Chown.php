<?php
/**
 *
 */

namespace Pants\Task;

use Pants\File,
    Pants\FileSets,
    Pants\FileSetTask,
    Pants\Task\AbstractTask;

/**
 *
 */
class Chown extends AbstractTask implements FileSetTask
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
     * Owner to set
     * @var string
     */
    protected $_owner;

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
        $this->getFile()
             ->setOwner($this->getOwner());

        foreach ($this->getFileSets() as $fileSet) {
            foreach ($fileSet as $file) {
                $file->setOwner($this->getOwner());
            }
        }

        return $this;
    }

    /**
     * Set the target file
     *
     * @param string|File $file
     * @return Chown
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
