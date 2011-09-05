<?php
/**
 *
 */

namespace Pants;

use Exception,
    FilesystemIterator,
    IteratorAggregate,
    RecursiveDirectoryIterator,
    RecursiveIteratorIterator,
    Pants\FileSet\DefaultIgnoreFilterIterator,
    Pants\FileSet\IncludeExcludeFilterIterator;

class FileSet implements IteratorAggregate
{

    protected $_addDefaultIgnore = true;

    protected $_directory;

    protected $_excludes = array();

    protected $_includes = array();

    public function getAddDefaultIgnore()
    {
        return $this->_addDefaultIgnore;
    }

    public function getDirectory()
    {
        return $this->_directory;
    }

    public function getExcludes()
    {
        return $this->_excludes;
    }

    public function getIncludes()
    {
        return $this->_includes;
    }

    public function getIterator()
    {
        if (!$this->getDirectory()) {
            throw new Exception();
        }

        // Create a recursive directory iterator
        $iterator = new RecursiveDirectoryIterator(
            $this->getDirectory()
        );

        $iterator->setFlags(FilesystemIterator::SKIP_DOTS);

        // Wrap the iterator with a default ignore filter iterator
        if ($this->getAddDefaultIgnore()) {
            $iterator = new DefaultIgnoreFilterIterator($iterator);
        }

        // Wrap the iterator with a recursive iterator iterator
        $iterator = new RecursiveIteratorIterator(
            $iterator
        );

        $iterator->setFlags(RecursiveIteratorIterator::CHILD_FIRST);

        // Wrap the iterator with an include/exclude filter iterator
        $iterator = new IncludeExcludeFilterIterator(
            $iterator
        );

        $iterator->setBaseDirectory($this->getDirectory())
                 ->setExcludes($this->getExcludes())
                 ->setIncludes($this->getIncludes());

        return $iterator;
    }

    public function setAddDefaultIgnore($addDefaultIgnore)
    {
        $this->_addDefaultIgnore = $addDefaultIgnore;
        return $this;
    }

    public function setDirectory($directory)
    {
        $this->_directory = $directory;
        return $this;
    }

    public function setExcludes(array $excludes = array())
    {
        $this->_excludes = $excludes;
        return $this;
    }

    public function setIncludes(array $includes = array())
    {
        $this->_includes = $includes;
        return $this;
    }

}
