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
 * Change file(s) permissions
 *
 * @package Pants
 * @subpackage Task
 */
class Chmod extends AbstractTask implements FileSetable
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
     * Mode to set
     * @var string
     */
    protected $_mode;

    /**
     * Add a file set
     *
     * @param Traversable $fileSet
     * @return Chmod
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
     * @return Chmod
     * @throws BuildException
     */
    public function execute()
    {
        if (!$this->getFile() && !$this->getFileSets()) {
            throw new BuildException("File is not set");
        }

        if (!$this->getMode()) {
            throw new BuildException("Mode is not set");
        }

        $file = $this->filterProperties($this->getFile());
        $mode = base_convert(
            $this->filterProperties($this->getMode()),
            8,
            10
        );

        if ($file) {
            $this->_chmod($file, $mode);
        }

        foreach ($this->getFileSets() as $fileSet) {
            foreach ($fileSet as $file) {
                $this->_chmod($file, $mode);
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
     * Get the mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->_mode;
    }

    /**
     * Set the target file
     *
     * @param string $file
     * @return Chmod
     */
    public function setFile($file)
    {
        $this->_file = $file;
        return $this;
    }

    /**
     * Set the mode
     *
     * @param string $mode
     * @return Chmod
     */
    public function setMode($mode)
    {
        $this->_mode = $mode;
        return $this;
    }

    /**
     * Chmod a file
     *
     * @param string $file
     * @param integer $mode
     */
    protected function _chmod($file, $mode)
    {
        return $this->_run(function() use ($file, $mode) {
            return chmod($file, $mode);
        });
    }

}
