<?php
/**
 *
 */

namespace Pants;

/**
 *
 */
class Autoloader
{

    /**
     * Class mapping
     * @var array
     */
    private $_classes = array(
        "Pants\Project",
        "Pants\Properties",
        "Pants\Properties\PropertyNameCycleException",
        "Pants\Target",
        "Pants\Targets",
        "Pants\Task",
        "Pants\Task\AbstractFileTask",
        "Pants\Task\AbstractTask",
        "Pants\Task\Call",
        "Pants\Task\Chgrp",
        "Pants\Task\Chmod",
        "Pants\Task\Chown",
        "Pants\Task\Copy",
        "Pants\Task\Delete",
        "Pants\Task\Exception",
        "Pants\Task\Exec",
        "Pants\Task\FileSet",
        "Pants\Task\Move",
        "Pants\Task\Output",
        "Pants\Task\PhpScript",
        "Pants\Task\Property",
        "Pants\Tasks"
    );

    /**
     * Path
     * @var string
     */
    private $_path;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_path = dirname(__DIR__) . "/";
    }

    /**
     * Autoloader
     *
     * @param string $className
     */
    public function autoload($className)
    {
        if (in_array($className, $this->getClasses())) {
            require_once $this->getPath() . str_replace("\\", DIRECTORY_SEPARATOR, $className) . ".php";
        }
    }

    /**
     * Get the classes
     *
     * @return array
     */
    public function getClasses()
    {
        return $this->_classes;
    }

    /**
     * Get the path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Register the autoloader
     */
    public function register()
    {
        spl_autoload_register(array($this, "autoload"));
    }

}
