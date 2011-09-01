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

namespace Pants\Task;

use Pants\Task\AbstractFileSystemTask,
    Pants\BuildException,
    Pile\Exception as PileException;

/**
 *
 */
class TokenFilter extends AbstractFileSystemTask
{

    /**
     * Target file
     * @var string
     */
    protected $_file;

    /**
     * Replacements
     * @var array
     */
    protected $_replacements = array();

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
     * Add a replacement
     *
     * @param string $token
     * @param string $value
     */
    public function addReplacement($token, $value)
    {
        $this->_replacements[$token] = $value;
        return $this;
    }

    /**
     * Get the replacements
     *
     * @return array
     */
    public function getReplacements()
    {
        return $this->_replacements;
    }

    /**
     * Execute the task
     *
     * @return Chgrp
     */
    public function execute()
    {
        $file = $this->filterProperties($this->getFile());

        $contents = $this->getFileSystem()
                         ->getContents($file);

        foreach ($this->getReplacements() as $token => $value) {
            $token = $this->filterProperties($token);
            $value = $this->filterProperties($value);

            $contents = str_replace(
                "@{$token}@",
                $value,
                $contents
            );
        }

        $this->getFileSystem()
             ->putContents($file, $contents);

        return $this;
    }

    /**
     * Set the target file
     *
     * @param string $file
     * @return TokenFilter
     */
    public function setFile($file)
    {
        $this->_file = $file;
        return $this;
    }

}
