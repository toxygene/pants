<?php
/**
 *
 */

namespace Pants;

use Pants\Task,
    Pants\Task\AbstractTask,
    Pants\Tasks;

/**
 *
 */
class Target extends AbstractTask
{

    /**
     * Description
     * @var string
     */
    protected $_description;

    /**
     * Name
     * @var string
     */
    protected $_name;

    /**
     * Tasks
     * @var Tasks
     */
    protected $_tasks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_tasks = new Tasks();
    }

    /**
     * Execute the target
     *
     * @return Target
     */
    public function execute()
    {
        foreach ($this->getTasks() as $task) {
            $task->execute();
        }

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get the tasks
     *
     * @return Tasks
     */
    public function getTasks()
    {
        return $this->_tasks;
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return Target
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * Set the name
     *
     * @param string $name
     * @return Target
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

}
