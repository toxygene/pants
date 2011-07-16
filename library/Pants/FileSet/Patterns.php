<?php
/**
 *
 */

namespace Pants\FileSet;

use ArrayIterator,
    IteratorAggregate,
    Pants\FileSet\Pattern;

/**
 *
 */
class Patterns implements IteratorAggregate
{

    /**
     * Patterns
     * @var array
     */
    protected $_patterns = array();

    /**
     * Add a pattern
     *
     * @param Pattern $pattern
     * @return Patterns
     */
    public function add(Pattern $pattern)
    {
        $this->_patterns[] = $pattern;
        return $this;
    }

    /**
     * Get an iterator
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_patterns);
    }

}
