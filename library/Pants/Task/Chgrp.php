<?php
/**
 * Pants
 *
 * Copyright (c) 2011, Justin Hendrickson
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * The names of its contributors may not be used to endorse or promote
 *       products derived from this software without specific prior written
 *       permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Justin Hendrickson <justin.hendrickson@gmail.com>
 */

namespace Pants\Task;

use Pants\BuildException,
    Pants\Task\FileSetable,
    Traversable;

/**
 * Change file(s) group task
 *
 * @package Pants
 * @subpackage Task
 */
class Chgrp extends AbstractTask implements FileSetable
{

    /**
     * Target file
     * @var string
     */
    protected $_file;

    /**
     * FileSets
     * @var array
     */
    protected $_fileSets = array();

    /**
     * Group to set
     * @var string
     */
    protected $_group;

    /**
     * Add a file set
     *
     * @param Traversable $fileSet
     * @return Chgrp
     */
    public function addFileSet(Traversable $fileSet)
    {
        $this->_fileSets[] = $fileSet;
        return $this;
    }

    /**
     * Create a file set tied to this task
     *
     * @return FileSet
     */
    public function createFileSet()
    {
        $fileSet = new FileSet();
        $this->_fileSets[] = $fileSet;
        return $fileSet;
    }

    /**
     * Execute the task
     *
     * @return Chgrp
     * @throws BuildException
     */
    public function execute()
    {
        if (!$this->getFile() && !$this->getFileSets()) {
            throw new BuildException("File is not set");
        }

        if (!$this->getGroup()) {
            throw new BuildException("Group is not set");
        }

        $file  = $this->filterProperties($this->getFile());
        $group = $this->filterProperties($this->getGroup());

        if ($file) {
            $this->_chgrp($file, $group);
        }

        foreach ($this->getFileSets() as $fileSet) {
            foreach ($fileSet as $file) {
                $this->_chgrp($file, $mode);
            }
        }

        return $this;
    }

    /**
     * Get the target file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * Get the file sets
     *
     * @return array
     */
    public function getFileSets()
    {
        return $this->_fileSets;
    }

    /**
     * Get the group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->_group;
    }

    /**
     * Set the target file
     *
     * @param string $file
     * @return Chgrp
     */
    public function setFile($file)
    {
        $this->_file = $file;
        return $this;
    }

    /**
     * Set the group
     *
     * @param string $group
     * @return Chgrp
     */
    public function setGroup($group)
    {
        $this->_group = $group;
        return $this;
    }

    /**
     * Chgrp a file
     *
     * @param string $file
     * @param string $group
     * @return boolean
     */
    protected function _chgrp($file, $group)
    {
        return $this->_run(function() use ($file, $group) {
            return chgrp($file, $group);
        });
    }

}
