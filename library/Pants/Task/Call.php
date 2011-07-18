<?php
/**
 *
 */

namespace Pants\Task;

use Pants\Task\AbstractTask;

/**
 *
 */
class Call extends AbstractTask
{

    /**
     * Name of target to call
     * @var string
     */
    protected $_target;

    /**
     * Execute the task
     *
     * @return Call
     */
    public function execute()
    {
        $this->getProject()
             ->execute(array($this->getTarget()));

        return $this;
    }

    /**
     * Get the name of the target to call
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->_target;
    }

    /**
     * Set the name of the target to call
     *
     * @param string $target
     * @return Call
     */
    public function setTarget($target)
    {
        $this->_target = $target;
        return $this;
    }

}
