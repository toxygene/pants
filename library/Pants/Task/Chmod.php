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
class Chmod extends AbstractTask implements FileSetTask
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
     * Mode to set
     * @var string
     */
    protected $_mode;

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
        $this->getFile()
             ->setPermission($this->getMode());

        foreach ($this->getFileSets() as $fileSet) {
            foreach ($fileSet as $file) {
                $file->setPermission($this->getMode());
            }
        }

        return $this;
    }

    /**
     * Set the target file
     *
     * @param string|File $file
     * @return Chmod
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
