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

use RecursiveFilterIterator;

/**
 * Default ignore filter iterator
 *
 * @package Pants\FileSet
 */
class DefaultIgnoreFilterIterator extends RecursiveFilterIterator
{

    /**
     * Base directory
     *
     * @var string
     */
    protected $baseDirectory;

    /**
     * Default patterns
     *
     * @var array
     */
    protected $patterns = array(
        '#^\/\.git$#',
        '#^\/\.gitignore$#',
        '#^\/\.svn$#',
        '#^\/.+?\.swp#'
    );

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

        foreach ($this->getPatterns() as $pattern) {
            if (preg_match($pattern, $pathname)) {
                return false;
            }
        }
        
        return true;
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
     * Get the patterns
     *
     * @return array
     */
    public function getPatterns()
    {
        return $this->patterns;
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
     * Set the patterns
     *
     * @param array $patterns
     * @return self
     */
    public function setPatterns(array $patterns)
    {
        $this->patterns = $patterns;
        return $this;
    }

}
