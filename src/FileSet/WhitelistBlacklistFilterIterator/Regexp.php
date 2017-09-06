<?php
/**
 * Pants
 *
 * Copyright (c) 2011-2017, Justin Hendrickson
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

namespace Pants\FileSet\WhitelistBlacklistFilterIterator;

use SplFileInfo;

/**
 * Matcher that does a regular expression match on the path
 *
 * @package Pants\FileSet\WhitelistBlacklistFilterIterator
 */
class Regexp implements Matcher
{

    /**
     * Base directory
     *
     * @var string
     */
    protected $baseDirectory;

    /**
     * Regular expression pattern to match against
     *
     * @var string
     */
    protected $pattern;
    
    /**
     * Constructor
     *
     * @param string $pattern
     */
    public function __construct($pattern = null)
    {
        $this->setPattern($pattern);
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
     * Get the regular expression pattern to match against
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * {@inheritDoc}
     */
    public function match(SplFileInfo $file)
    {
        $path = preg_replace(
            '#^' . preg_quote($this->getBaseDirectory(), '#') . '/#',
            '',
            $file->getPathname()
        );

        return preg_match($this->getPattern(), $path) > 0;
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
     * Set the regular expression pattern to match against
     *
     * @param string $pattern
     * @return self
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
        return $this;
    }

}
