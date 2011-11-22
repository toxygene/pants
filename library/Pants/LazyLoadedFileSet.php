<?php
/**
 *
 */

namespace Pants;

use IteratorAggregate,
    IteratorIterator,
    Pants\Types;

/**
 *
 */
class LazyLoadedFileSet implements IteratorAggregate
{

    /**
     * Constructor
     *
     * @param Types $types
     * @param string $id
     */
    public function __construct(Types $types, $id)
    {
        $this->_types = $types;
        $this->_id    = $id;
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
     * Get the iterator
     *
     * @return IteratorIterator
     */
    public function getIterator()
    {
        return new IteratorIterator($this->getTypes()->{$this->getId()});
    }

    /**
     * Get the types
     *
     * @return Types
     */
    public function getTypes()
    {
        return $this->_types;
    }

}
