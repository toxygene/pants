<?php
/**
 *
 */

namespace Pants;

use FilesystemIterator,
    Pants\FileSet\Patterns,
    Pants\FileSet\PatternFilterIterator,
    IteratorAggregate;

/**
 *
 */
class FileSet implements IteratorAggregate
{

    /**
     * Directory
     * @var string
     */
    protected $_directory;

    /**
     * Id
     * @var string
     */
    protected $_id;

    /**
     * Exclude patterns
     * @var Patterns
     */
    protected $_excludePatterns;

    /**
     * Include patterns
     * @var Patterns
     */
    protected $_includePatterns;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_excludePatterns = new Patterns();
        $this->_includePatterns = new Patterns();
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
     * Get the exclude patterns
     *
     * @return Patterns
     */
    public function getExcludePatterns()
    {
        return $this->_excludePatterns;
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
        return $this->_includePatterns;
    }

    /**
     * Get the iterator
     *
     * @return PatternFilterIterator
     */
    public function getIterator()
    {
        return new PatternFilterIterator(
            new FilesystemIterator($this->getDirectory()),
            $this->getExcludePatterns(),
            $this->getIncludePatterns()
        );
    }

    /**
     * Set the directory
     *
     * @param string $directory
     * @return FileSet
     */
    public function setDirectory($directory)
    {
        $this->_directory = $directory;
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
