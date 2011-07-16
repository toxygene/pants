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
class Delete extends AbstractTask implements FileSetTask
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
     * Execute the task
     *
     * @return Delete
     */
    public function execute()
    {
        $this->getFile()
             ->delete();

        foreach ($this->getFileSets() as $fileSet) {
            foreach ($fileSet as $file) {
                $file->delete();
            }
        }

        return $this;
    }

    /**
     * Set the target file
     *
     * @param string|File $file
     * @return Delete
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
