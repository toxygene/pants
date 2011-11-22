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
    Pants\FileSet as FileSetType,
    Pants\FileSet\Pattern,
    Pants\Task\AbstractTask,
    StdClass;

/**
 * FileSet task
 *
 * @package Pants
 * @subpackage Task
 */
class FileSet extends AbstractTask
{

    /**
     * Base directory
     * @var string
     */
    protected $_baseDirectory;

    /**
     * Id
     * @var string
     */
    protected $_id;

    /**
     *
     */
    public function createInclude()
    {
        $include = new Pattern;
        $this->_includes[] = $include;
        return $include;
    }

    /**
     * Execute a task
     *
     * @throws BuildException
     */
    public function execute()
    {
        if (!$this->getId()) {
            throw new BuildException("No id set");
        }

        $fileSet = new FileSetType();

        $fileSet->setBaseDirectory($this->getBaseDirectory());

        $fileSet->setIncludes(array_map(
            function($include) {
                return $include->getPattern();
            },
            $this->_includes
        ));

        // TODO populate file set object

        $this->getProject()
             ->getTypes()
             ->{$this->getId()} = $fileSet;
    }

    /**
     * Get the base directory
     *
     * @return string
     */
    public function getBaseDirectory()
    {
        return $this->_baseDirectory;
    }

    /**
     * Get the id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set the base directory
     *
     * @param string $baseDirectory
     * @return FileSet
     */
    public function setBaseDirectory($baseDirectory)
    {
        $this->_baseDirectory = $baseDirectory;
        return $this;
    }

    /**
     * Set the id
     *
     * @param string $id
     * @return FileSet
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

}
