<?php
/**
 *
 */

namespace Pants\FileSet;

use ExcludePatterns,
    FilterIterator,
    IncludePatterns,
    Iterator;

/**
 *
 */
class PatternFilterIterator extends FilterIterator
{

    /**
     * Constructor
     *
     * @param Iterator $iterator
     * @param ExcludePatterns $excludes
     * @param IncludePatterns $includes
     */
    public function __construct(Iterator $iterator, ExcludePatterns $excludes, IncludePatterns $includes)
    {
        parent::__construct($iterator);
        $this->_excludes = $excludes;
        $this->_includes = $includes;
    }

    /**
     * Determine if the current element is accepted
     *
     * @return boolean
     */
    public function accept()
    {
        foreach ($this->_includes as $include) {
            if ($include->isMatch($this->current())) {
                foreach ($this->_excludes as $exclude) {
                    if ($exclude->isMatch($this->current())) {
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }

}
