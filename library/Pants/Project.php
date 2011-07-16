<?php
/**
 *
 */

namespace Pants;

use Pants\FileSets,
    Pants\Properties,
    Pants\Targets;

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
     * FileSets
     * @var FileSets
     */
    protected $_fileSets;

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
     * Constructor
     */
    public function __construct()
    {
        $this->_fileSets   = new FileSets();
        $this->_properties = new Properties();
        $this->_targets    = new Targets($this);
    }

    /**
     * Add a task
     *
     * @param Task $task
     * @return Project
     */
    public function addTask(Task $task)
    {
        $task->setProject($this)
             ->execute();

        return $this;
    }

    /**
     * Execute targets
     *
     * @param array $targets
     * @return Project
     */
    public function execute($targets = array())
    {
        if (!$targets) {
            $targets = array($this->getDefault());
        }

        foreach ($targets as $target) {
            $this->getTargets()
                 ->$target
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
     * Get the filesets
     *
     * @return FileSets
     */
    public function getFileSets()
    {
        return $this->_fileSets;
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
