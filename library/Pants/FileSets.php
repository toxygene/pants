<?php
/**
 *
 */

namespace Pants;

use InvalidArgumentException,
    IteratorAggregate,
    Pants\FileSet;

/**
 *
 */
class FileSets implements IteratorAggregate
{

    /**
     * FileSets
     * @var array
     */
    protected $_fileSets = array();

    /**
     * Get a fileset
     *
     * @param string $id
     * @return FileSet
     * @throws InvalidArgumentException
     */
    public function __get($id)
    {
        if (!isset($this->$id)) {
            throw new InvalidArgumentException("There is no fileset with the id of '{$id}'");
        }

        return $this->_fileSets[$id];
    }

    /**
     * Check if a fileset exists
     *
     * @param string $id
     * @return boolean
     */
    public function __isset($id)
    {
        return isset($this->_fileSets[$id]);
    }

    /**
     * Set a fileset
     *
     * @param string $id
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    public function __set($id, $value)
    {
        if (!$value instanceof FileSet) {
            throw new InvalidArgumentException("The value must be a fileset");
        }

        $this->_fileSets[$id] = $id;
    }

    /**
     * Unset a fileset
     *
     * @param string $id
     * @throws InvalidArgumentException
     */
    public function __unset($id)
    {
        if (!isset($this->$id)) {
            throw new InvalidArgumentException("There is no fileset with the id of '{$id}'");
        }

        unset($this->_fileSets[$id]);
    }

    /**
     * Add a fileset
     *
     * @param FileSet $fileset
     * @return FileSet
     * @throws InvalidArgumentException
     */
    public function add(FileSet $fileset)
    {
        if (isset($this->{$fileset->getId()})) {
            throw new InvalidArgumentException("A fileset already exists with the id of '{$fileset->getId()}'");
        }

        $this->{$fileset->getId()} = $fileset;

        return $this;
    }

    /**
     * Get an iterator
     *
     * return Iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator(array());
    }

}
