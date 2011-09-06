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

use RecursiveFilterIterator,
    RecursiveIterator;

/**
 * Default ignore filter iterator
 *
 * @package Pants
 * @subpackage FileSet
 */
class DefaultIgnoreFilterIterator extends RecursiveFilterIterator
{

    /**
     * Default patterns
     * @var array
     */
    protected $_patterns = array(
        "^\.git$",
        "^\.svn$"
    );

    /**
     * Constructor
     *
     * @param RecursiveIterator $iterator
     * @param array $patterns
     */
    public function __construct(RecursiveIterator $iterator, $patterns = array())
    {
        if ($patterns) {
            $this->_patterns = $patterns;
        }

        parent::__construct($iterator);
    }

    /**
     * Check whether the current element of the iterator is acceptable
     *
     * @return boolean
     */
    public function accept()
    {
        $pattern = "";
        foreach ($this->getPatterns() as $p) {
            if ($pattern) {
                $pattern .= "|";
            }

            $pattern .= "({$p})";
        }

        return preg_match("#{$pattern}#", $this->getInnerIterator()->current()->getFilename()) == 0;
    }

    /**
     * Get the patterns
     *
     * @return array
     */
    public function getPatterns()
    {
        return $this->_patterns;
    }

}
