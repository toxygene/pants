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
 * Whitelist/blacklist pattern filter iterator
 *
 * @package Pants\FileSet
 */
class WhitelistBlacklistFilterIterator extends FilterIterator
{

    /**
     * Blacklist matchers
     *
     * @var Pants\FileSet\WhitelistBlacklistFilterIterator\Matcher[]
     */
    protected $blacklistMatchers = array();

    /**
     * Whitelist matchers
     *
     * @var Pants\FileSet\WhitelistBlacklistFilterIterator\Matcher[]
     */
    protected $whitelistMatchers = array();

    /**
     * Check whether the current element of the iterator is acceptable
     *
     * @return boolean
     */
    public function accept()
    {
        if ($this->getWhitelistMatchers()) {
            foreach ($this->getWhitelistMatchers() as $whitelist) {
                if ($whitelist->match($this->getInnerIterator()->current())) {
                    foreach ($this->getBlacklistMatchers() as $blacklist) {
                        if ($blacklist->match($this->getInnerIterator()->current())) {
                            return false;
                        }
                    }
                    return true;
                }
            }
            
            return false;
        } else {
            foreach ($this->getBlacklistMatchers() as $blacklist) {
                if ($blacklist->match($this->getInnerIterator()->current())) {
                    return false;
                }
            }
            
            return true;
        }
    }

    /**
     * Get the blacklist matchers
     *
     * @return Pants\FileSet\WhitelistBlacklistFilterIterator\Matcher[]
     */
    public function getBlacklistMatchers()
    {
        return $this->blacklistMatchers;
    }

    /**
     * Get the whitelist matchers
     *
     * @return Pants\FileSet\WhitelistBlacklistFilterIterator\Matcher[]
     */
    public function getWhitelistMatchers()
    {
        return $this->whitelistMatchers;
    }

    /**
     * Set the exclude patterns
     *
     * @param Pants\FileSet\WhitelistBlacklistFilterIterator\Matcher[] $excludes
     * @return self
     */
    public function setBlacklistMatchers(array $blacklistMatchers)
    {
        $this->blacklistMatchers = $blacklistMatchers;
        return $this;
    }

    /**
     * Set the include patterns
     *
     * @param Pants\FileSet\WhitelistBlacklistFilterIterator\Matcher[] $includes
     * @return self
     */
    public function setWhitelistMatchers(array $whitelistMatchers)
    {
        $this->whitelistMatchers = $whitelistMatchers;
        return $this;
    }

}
