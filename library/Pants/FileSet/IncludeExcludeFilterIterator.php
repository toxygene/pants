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
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 'AS IS'
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
 */

namespace Pants\FileSet;

use FilterIterator;

/**
 * Include/exclude pattern filter iterator
 *
 * @package Pants\FileSet
 */
class IncludeExcludeFilterIterator extends FilterIterator
{

    /**
     * Base directory
     *
     * @var string
     */
    protected $baseDirectory;

    /**
     * Exclude patterns
     *
     * @var Pants\FileSet\IncludeExcludeFilterIterator\Matcher[]
     */
    protected $excludes = array();

    /**
     * Include patterns
     *
     * @var Pants\FileSet\IncludeExcludeFilterIterator\Matcher[]
     */
    protected $includes = array();

    /**
     * Check whether the current element of the iterator is acceptable
     *
     * @return boolean
     */
    public function accept()
    {
        $pathname = preg_replace(
            '#^' . preg_quote($this->getBaseDirectory()) . '/?#',
            '/',
            $this->getInnerIterator()
                ->current()
                ->getPathname()
        );

        foreach ($this->getIncludes() as $include) {
            if ($include->match($pathname)) {
                foreach ($this->getExcludes() as $exclude) {
                    $match = $exclude->match($pathname);
                    if ($match) {
                        return false;
                    }
                }
                return true;
            }
        }

        return false;
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
     * @return Pants\FileSet\IncludeExcludeFilterIterator\Matcher[]
     */
    public function getExcludes()
    {
        return $this->excludes;
    }

    /**
     * Get the include patterns
     *
     * @return Pants\FileSet\IncludeExcludeFilterIterator\Matcher[]
     */
    public function getIncludes()
    {
        return $this->includes;
    }

    /**
     * Set the base directory
     *
     * @param string $baseDirectory
     * @return self
     */
    public function setBaseDirectory($baseDirectory)
    {
        $this->baseDirectory = $baseDirectory;
        return $this;
    }

    /**
     * Set the exclude patterns
     *
     * @param Pants\FileSet\IncludeExcludeFilterIterator\Matcher[] $excludes
     * @return self
     */
    public function setExcludes(array $excludes)
    {
        $this->excludes = $excludes;
        return $this;
    }

    /**
     * Set the include patterns
     *
     * @param Pants\FileSet\IncludeExcludeFilterIterator\Matcher[] $includes
     * @return self
     */
    public function setIncludes(array $includes)
    {
        $this->includes = $includes;
        return $this;
    }

}
