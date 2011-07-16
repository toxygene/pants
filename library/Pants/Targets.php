<?php
/**
 *
 */

namespace Pants;

use InvalidArgumentException,
    Pants\Project,
    Pants\Target;

/**
 *
 */
class Targets
{

    /**
     * Project
     * @var Project
     */
    protected $_project;

    /**
     * Targets
     * @var array
     */
    protected $_targets = array();

    /**
     * Constructor
     *
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->_project = $project;
    }

    /**
     * Get a target
     *
     * @param string $name
     * @return Target
     * @throws InvalidArgumentException
     */
    public function __get($name)
    {
        if (!isset($this->$name)) {
            throw new InvalidArgumentException("There is no target with the name of '{$name}'");
        }

        return $this->_targets[$name];
    }

    /**
     * Check if a target exists
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->_targets[$name]);
    }

    /**
     * Set a target
     *
     * @param string $name
     * @param mixed $value
     * @throws InvalidArgumentException
     */
    public function __set($name, $value)
    {
        if (!$value instanceof Target) {
            throw new InvalidArgumentException("The value must be a target");
        }

        $value->setProject($this->getProject());

        $this->_targets[$name] = $value;
    }

    /**
     * Unset a target
     *
     * @param string $name
     * @throws InvalidArgumentException
     */
    public function __unset($name)
    {
        if (!isset($this->$name)) {
            throw new InvalidArgumentException("There is no target with the name of '{$name}'");
        }

        unset($this->_targets[$name]);
    }

    /**
     * Add a target
     *
     * @param Target $target
     * @return Targets
     * @throws InvalidArgumentException
     */
    public function add(Target $target)
    {
        if (isset($this->{$target->getName()})) {
            throw new InvalidArgumentException("A target already exists with the name of '{$target->getName()}'");
        }

        $this->{$target->getName()} = $target;

        return $this;
    }

    /**
     * Get the project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->_project;
    }

}
