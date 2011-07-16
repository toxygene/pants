<?php
/**
 *
 */

namespace Pants\Task;

use Pants\Task\AbstractTask;

/**
 *
 */
class Output extends AbstractTask
{

    /**
     * Message to display
     * @var string
     */
    protected $_message;

    /**
     * Get the message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * Execute the task
     *
     * @return Output
     */
    public function execute()
    {
        echo $this->getProject()
                  ->getProperties()
                  ->filter($this->getMessage());

        return $this;
    }

    /**
     * Set the message
     *
     * @param string $message
     * @return Output
     */
    public function setMessage($message)
    {
        $this->_message = $message;
        return $this;
    }

}
