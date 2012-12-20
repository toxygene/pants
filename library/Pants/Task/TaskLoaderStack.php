<?php

namespace Pants\Task;

use Zend\Loader\ShortNameLocator;

class TaskLoaderStack implements ShortNameLocator
{

    /**
     * Task loaders
     *
     * @var ShortNameLocator[]
     */
    protected $taskLoaders = array();

    /**
     * Add a task loader
     *
     * @param ShortNameLocator $taskLoader
     * @return self
     */
    public function add(ShortNameLocator $taskLoader)
    {
        $this->taskLoaders[] = $taskLoader;
        return $this;
    }

    /**
     * Whether or not a Helper by a specific name
     *
     * @param  string $name
     * @return bool
     */
    public function isLoaded($name)
    {
        foreach ($this->taskLoaders as $taskLoader) {
            if ($taskLoader->isLoaded($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return full class name for a named helper
     *
     * @param  string $name
     * @return string
     */
    public function getClassName($name)
    {
        foreach ($this->taskLoaders as $taskLoader) {
            $className = $taskLoader->getClassName($name);
            if ($className) {
                return $className;
            }
        }

        return false;
    }

    /**
     * Load a helper via the name provided
     *
     * @param  string $name
     * @return string
     */
    public function load($name)
    {
        foreach ($this->taskLoaders as $taskLoader) {
            $className = $taskLoader->load($name);
            if ($className) {
                return $className;
            }
        }

        return false;
    }

}