<?php
/**
 *
 */

namespace Pants\Task;

use Pants\Task\AbstractTask;

/**
 *
 */
class Exec extends AbstractTask
{

    /**
     * Command to execute
     * @var string
     */
    protected $_command;

    /**
     * Directory to execute the command in
     * @var string
     */
    protected $_directory;

    /**
     * Set the command to execute
     *
     * @param string $command
     * @return Exec
     */
    public function setCommand($command)
    {
        $this->_command = $command;
        return $this;
    }

    /**
     * Set the directory to execute the command in
     *
     * @param string $directory
     * @return Exec
     */
    public function setDirectory($directory)
    {
        $this->_directory = $directory;
        return $this;
    }

    /**
     * Execute the task
     *
     * @return Exec
     */
    public function exec()
    {
        if ($this->getDirectory() && !chdir($this->getDirectory())) {
            throw new BuildException("Could not change the directory to '{$this->getDirectory()}'");
        }

        if (!exec($command)) {
            throw new BuildException("Could not execute '{$command}'");
        }

        return $this;
    }

}
