<?php
/**
 *
 */

namespace Pants;

use Pants\Task\AbstractTask,
    Pile\FileSystem;

/**
 *
 */
class AbstractFileTask extends AbstractTask
{

    /**
     * FileSystem object
     * @var FileSystem
     */
    private $_fileSystem;

    /**
     * Get the file system
     *
     * @return FileSystem
     */
    public function getFileSystem()
    {
        if (!$this->_fileSystem) {
            $this->_fileSystem = new FileSystem();
        }
        return $this->_fileSystem;
    }

    /**
     * Set the file system
     *
     * @param FileSystem $fileSystem
     * @return AbstractFileTask
     */
    public function setFileSystem(FileSystem $fileSystem)
    {
        $this->_fileSystem = $fileSystem;
        return $this;
    }

}
