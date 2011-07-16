<?php
/**
 *
 */

namespace Pants;

use Pants\Project;

/**
 *
 */
interface Task
{

    /**
     * Execute the task
     *
     * @return Task
     */
    public function execute();

    /**
     * Set the project
     *
     * @param Project $project
     * @return Task
     */
    public function setProject(Project $project);

}
