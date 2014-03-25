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

use Pale\Pale;
use Pants\BuildException;
use Pants\Task\FileSetable;
use Traversable;

/**
 * Delete file(s) task
 *
 * @package Pants
 * @subpackage Task
 */
class Delete extends AbstractTask implements FileSetable
{

    /**
     * Target file
     *
     * @var string
     */
    protected $file;

    /**
     * FileSets
     *
     * @var array
     */
    protected $fileSets = array();

    /**
     * Add a file set
     *
     * @param Traversable $fileSet
     * @return Delete
     */
    public function addFileSet(Traversable $fileSet)
    {
        $this->fileSets[] = $fileSet;
        return $this;
    }

    /**
     * Create a fileset tied to this task
     *
     * @return FileSet
     */
    public function createFileset()
    {
        $fileSet = new FileSet();
        $this->fileSets[] = $fileSet;
        return $fileSet;
    }

    /**
     * Execute the task
     *
     * @return Delete
     * @throws BuildException
     */
    public function execute()
    {
        if (!$this->getFile() && !$this->getFileSets()) {
            throw new BuildException("File not set");
        }

        $file = $this->filterProperties($this->getFile());

        if ($file) {
            Pale::run(function() use ($file) {
                return unlink($file);
            });
        }

        foreach ($this->fileSets as $fileSet) {
            foreach ($fileSet as $file) {
                Pale::run(function() use ($file) {
                    return unlink($file);
                });
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
        return $this->file;
    }

    /**
     * Get the file sets
     *
     * @return array
     */
    public function getFileSets()
    {
        return $this->fileSets;
    }

    /**
     * Set the target file
     *
     * @param string $file
     * @return Delete
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

}
