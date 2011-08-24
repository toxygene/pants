<?php
/**
 *
 */

namespace Pants;

use Pants\Properties,
    Pants\Targets,
    Pants\Tasks;

/**
 *
 */
class Project
{

    /**
     * Default task name
     * @var string
     */
    protected $_default;

    /**
     * Properties
     * @var Properties
     */
    protected $_properties;

    /**
     * Targets
     * @var Targets
     */
    protected $_targets;

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
        $this->_properties = new Properties();
        $this->_targets    = new Targets();
        $this->_tasks      = new Tasks();
    }

    /**
     * Execute targets
     *
     * @param array $targets
     * @return Project
     */
    public function execute($targets = array())
    {
        foreach ($this->getTasks() as $task) {
            $task->setProject($this)
                 ->execute();
        }

        if (!$targets) {
            $targets = array($this->getDefault());
        }

        foreach ($targets as $target) {
            $this->getTargets()
                 ->$target
                 ->setProject($this)
                 ->execute();
        }

        return $this;
    }

    /**
     * Get the default target name
     *
     * @return string
     */
    public function getDefault()
    {
        return $this->_default;
    }

    /**
     * Get the properties
     *
     * @return Properties
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    /**
     * Get the targets
     *
     * @return Targets
     */
    public function getTargets()
    {
        return $this->_targets;
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
     * Set the default target name
     *
     * @param string $default
     * @return Project
     */
    public function setDefault($default)
    {
        $this->_default = $default;
        return $this;
    }

}
