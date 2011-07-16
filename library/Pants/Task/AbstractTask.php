<?php
/**
 *
 */

namespace Pants\Task;

use Pants\Project,
    Pants\Task;

/**
 *
 */
abstract class AbstractTask implements Task
{

    /**
     * Project
     * @var Project
     */
    protected $_project;

    /**
     * Get the project
     * @return Project
     */
    public function getProject()
    {
        return $this->_project;
    }

    /**
     * Set the project
     *
     * @param Project $project
     * @return Task
     */
    public function setProject(Project $project)
    {
        $this->_project = $project;
        return $this;
    }

}
