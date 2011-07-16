<?php
/**
 *
 */

namespace Pants;

use Pants\File\RuntimeException,
    SplFileObject;

/**
 *
 */
class File extends SplFileObject
{

    /**
     * Delete this file
     */
    public function delete()
    {
        if (!unlink($this->getRealPath())) {
            throw new RuntimeException("Could not delete the file");
        }
    }

    /**
     * Move this file and return a new File object
     *
     * @param string $destination
     * @return File
     */
    public function move($destination)
    {
        if (!rename($this->getRealPath(), $destination)) {
            throw new RuntimeException("Could not move the file to destination '{$destination}'");
        }

        return new self($destination);
    }

    /**
     * Set the owner of the file
     *
     * @param string $owner
     * @return File
     */
    public function setOwner($owner)
    {
        if (!chown($this->getRealPath(), $owner)) {
            throw new RuntimeException("Could not set the owner '{$owner}' on file '{$this->getRealPath()}'");
        }

        return $this;
    }

    /**
     * Set the group of the file
     *
     * @param string $group
     * @return File
     */
    public function setGroup($group)
    {
        if (!chgrp($this->getRealPath(), $group)) {
            throw new RuntimeException("Could not set the group '{$group}' on file '{$this->getRealPath()}'");
        }

        return $this;
    }

}
