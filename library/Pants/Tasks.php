<?php
/**
 *
 */

namespace Pants;

use ArrayIterator,
    InvalidArgumentException,
    IteratorAggregate,
    Pants\Task;

/**
 *
 */
class Tasks implements IteratorAggregate
{

    /**
     * Tasks
     * @var array
     */
    protected $_tasks = array();

    /**
     * Add a task
     *
     * @param Task $task
     * @return Tasks
     */
    public function add(Task $task)
    {
        $this->_tasks[] = $task;
        return $this;
    }

    /**
     * Get an iterator
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_tasks);
    }

}
