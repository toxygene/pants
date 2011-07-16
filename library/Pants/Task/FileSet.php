<?php
/**
 *
 */

namespace Pants;

use Pants\FileSet as FS;

/**
 *
 */
class FileSet extends AbstractTask
{

    /**
     * FileSet
     * @var FileSet
     */
    protected $_fileSet;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_fileSet = new FS();
    }

    /**
     * Get the exclude patterns
     *
     * @return Patterns
     */
    public function getExcludePatterns()
    {
        return $this->getFileSet()
                    ->getExcludePatterns();
    }

    /**
     * Get the directory
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->_directory;
    }

    /**
     * Get the fileset
     *
     * @return FileSet
     */
    public function getFileSet()
    {
        return $this->_fileSet;
    }

    /**
     * Get the id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get the include patterns
     *
     * @return Patterns
     */
    public function getIncludePatterns()
    {
        return $this->getFileSet()
                    ->getIncludePatterns();
    }

    /**
     * Execute the task
     *
     * @return FileSet
     */
    public function execute()
    {
        $this->getProject()
             ->getFileSets()
             ->add($this->getFileSet());

        return $this;
    }

    /**
     * Set the directory
     *
     * @param string $directory
     * @return FileSet
     */
    public function setDirectory($directory)
    {
        $this->getFileSet()
             ->setDirectory($directory);

        return $this;
    }

    /**
     * Set the id
     *
     * @param string $id
     * @return FileSet
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

}
