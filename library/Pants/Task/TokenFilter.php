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

use Pants\BuildException;

/**
 *
 */
class TokenFilter extends AbstractTask
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
     * Execute the task
     *
     * @return TokenFilter
     * @throws BuildException
     */
    public function execute()
    {
        if (!$this->getFile()) {
            throw new BuildException("File not set");
        }

        $file = $this->filterProperties($this->getFile());
        $replacements = $this->getReplacements();

        $this->_run(function() use ($file, $replacements) {
            $contents = file_get_contents($file);

            foreach ($replacements as $token => $value) {
                $contents = str_replace("@{$token}@", $value, $contents);
            }

            return file_put_contents(
                $file,
                $contents
            );
        });

        return $this;
    }

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
     * @return TokenFilter
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
