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

namespace Pants\FileSet;

use Exception;
use FilesystemIterator;
use IteratorAggregate;
use Pants\FileSet\DefaultIgnoreFilterIterator;
use Pants\FileSet\DotFilterIterator;
use Pants\FileSet\IncludeExcludeFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Abstraction of a set of files
 *
 * @package Pants\FileSet
 */
class FileSet implements IteratorAggregate
{

    /**
     * Add default ignore flag
     *
     * @var boolean
     */
    protected $addDefaultIgnore = true;

    /**
     * Base directory
     *
     * @var string
     */
    protected $baseDirectory;

    /**
     * Exclude patterns
     *
     * @var array
     */
    protected $excludes = array();

    /**
     * Include patterns
     *
     * @var array
     */
    protected $includes = array();

    /**
     * Get the add default ignore flag
     *
     * @return boolean
     */
    public function getAddDefaultIgnore()
    {
        return $this->addDefaultIgnore;
    }

    /**
     * Get the base directory
     *
     * @return string
     */
    public function getBaseDirectory()
    {
        return $this->baseDirectory;
    }

    /**
     * Get the exclude patterns
     *
     * @return array
     */
    public function getExcludes()
    {
        return $this->excludes;
    }

    /**
     * Get the include patterns
     *
     * @return array
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    /**
     * Retrieve an external iterator
     *
     * @return Iterator
     */
    public function getIterator()
    {
        if (!$this->getBaseDirectory()) {
            throw new BuildException("No base directory is set");
        }

        // Create a recursive directory iterator
        $iterator = new RecursiveDirectoryIterator(
            $this->getBaseDirectory()
        );

        // Optionally wrap the iterator with a default ignore filter iterator
        if ($this->getAddDefaultIgnore()) {
            $iterator = new DefaultIgnoreFilterIterator($iterator);
        }

        // Wrap the iterator with a recursive iterator iterator
        $iterator = new RecursiveIteratorIterator(
            $iterator,
            RecursiveIteratorIterator::CHILD_FIRST
        );

        // Wrap the iterator with an include/exclude filter iterator
        $iterator = new IncludeExcludeFilterIterator(
            $iterator
        );

        $iterator->setBaseDirectory($this->getBaseDirectory())
            ->setExcludes($this->getExcludes())
            ->setIncludes($this->getIncludes());

        return $iterator;
    }

    /**
     * Set the add default ignore flag
     *
     * @param boolean $addDefaultIgnore
     * @return FileSet
     */
    public function setAddDefaultIgnore($addDefaultIgnore)
    {
        $this->addDefaultIgnore = $addDefaultIgnore;
        return $this;
    }

    /**
     * Set the base directory
     *
     * @param string $baseDirectory
     * @return FileSet
     */
    public function setBaseDirectory($baseDirectory)
    {
        $this->baseDirectory = $baseDirectory;
        return $this;
    }

    /**
     * Set the exclude patterns
     *
     * @param array $excludes
     * @return FileSet
     */
    public function setExcludes(array $excludes = array())
    {
        $this->excludes = $excludes;
        return $this;
    }

    /**
     * Set the include patterns
     *
     * @param array $includes
     * @return FileSet
     */
    public function setIncludes(array $includes = array())
    {
        $this->includes = $includes;
        return $this;
    }

}
