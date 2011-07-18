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
     * Copy this file
     *
     * @param string $destination
     * @return File
     */
    public function copy($destination)
    {
        if (!copy($this->getRealPath())) {
            throw new RuntimeException("Could not copy the file");
        }

        return new self($destination);
    }

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
     * Move this file
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

    /**
     * Set the permissions of the file
     *
     * @param mixed $mode
     * @return File
     * @TODO Add support for ugoa+rwxX
     */
    public function setPermission($mode)
    {
        if (!chmod($this->getRealPath(), $mode)) {
            throw new RuntimeException("Could not se the permission '{$mode}' on file '{$this->getRealPath()}'");
        }

        return $this;
    }

    /**
     * Symlink a file
     *
     * @param string $target
     * @return File
     */
    public function symlink($target)
    {
        if (!symlink($this->getRealPath(), $target)) {
            throw new RuntimeException("Could not create symlink '{$target}' of file '{$this->getRealPath()}'");
        }

        return $this;
    }

    /**
     * Touch a file
     *
     * @return File
     */
    public function touch()
    {
        if (!touch($this->getRealPath())) {
            throw new RuntimeException("Could not touch file '{$this->getRealPath()}'");
        }

        return $this;
    }

}
