<?php
/**
 *
 */

namespace Pants;

use Pants\Task;

/**
 *
 */
interface FileSetTask extends Task
{

    /**
     * Get the target filesets
     *
     * @return array
     */
    public function getFilesets();

}
