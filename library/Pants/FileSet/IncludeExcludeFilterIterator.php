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
 */

namespace Pants\FileSet;

use FilterIterator;

/**
 * Include/exclude pattern filter iterator
 *
 * @package Pants
 * @subpackage FileSet
 */
class IncludeExcludeFilterIterator extends FilterIterator
{

    /**
     * Base directory
     * @var string
     */
    protected $_baseDirectory;

    /**
     * Exclude patterns
     * @var array
     */
    protected $_excludes = array();

    /**
     * Include patterns
     * @var array
     */
    protected $_includes = array();

    /**
     * Check whether the current element of the iterator is acceptable
     *
     * @return boolean
     */
    public function accept()
    {
        $path = preg_replace(
            "#^" . preg_quote($this->getBaseDirectory()) . "/?#",
            "",
            $this->getInnerIterator()->current()->getPathname()
        );

        foreach ($this->_includes as $include) {
            if (preg_match($include, $path)) {
                foreach ($this->_excludes as $exclude) {
                    if (preg_match($exclude, $path)) {
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
        return $this->_baseDirectory;
    }

    /**
     * Set the base directory
     *
     * @param string $baseDirectory
     * @return IncludeExcludeFilterIterator
     */
    public function setBaseDirectory($baseDirectory)
    {
        $this->_baseDirectory = $baseDirectory;
        return $this;
    }

    public function setExcludes(array $excludes = array())
    {
        $this->_excludes = $excludes;
        return $this;
    }

    public function setIncludes(array $includes = array())
    {
        $this->_includes = $includes;
        return $this;
    }

}
