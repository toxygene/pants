<?php
/**
 *
 */

namespace Pants\FileSet;

use FilterIterator;

class IncludeExcludeFilterIterator extends FilterIterator
{

    /**
     * Base directory
     * @var string
     */
    protected $_baseDirectory;

    /**
     * Exclude patterns
     * @var array
     */
    protected $_excludes = array();

    /**
     * Include patterns
     * @var array
     */
    protected $_includes = array();

    /**
     *
     * @return boolean
     */
    public function accept()
    {
        $path = preg_replace(
            "#^" . preg_quote($this->getBaseDirectory()) . "/?#",
            "",
            $this->getInnerIterator()->current()->getPathname()
        );

        foreach ($this->_includes as $include) {
            if (preg_match($include, $path)) {
                foreach ($this->_excludes as $exclude) {
                    if (preg_match($exclude, $path)) {
                        return false;
                    }
                }
                return true;
            }
        }

        return false;
    }

    /**
     * Get the base directory
     *
     * @return string
     */
    public function getBaseDirectory()
    {
        return $this->_baseDirectory;
    }

    /**
     * Set the base directory
     *
     * @param string $baseDirectory
     * @return IncludeExcludeFilterIterator
     */
    public function setBaseDirectory($baseDirectory)
    {
        $this->_baseDirectory = $baseDirectory;
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
