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

use Pale\Pale;
use Pants\BuildException;

/**
 * Replace tokens in file(s) task
 *
 * @package Pants\Task
 */
class TokenFilter extends AbstractTask
{

    /**
     * Ending character
     *
     * @var string
     */
    protected $endingCharacter = "@";

    /**
     * Target file
     *
     * @var string
     */
    protected $file;

    /**
     * Replacements
     *
     * @var array
     */
    protected $replacements = array();

    /**
     * Starting character
     *
     * @var string
     */
    protected $startingCharacter = "@";

    /**
     * Add a replacement
     *
     * @param string $token
     * @param string $value
     * @return TokenFilter
     */
    public function addReplacement($token, $value)
    {
        $this->replacements[$token] = $value;
        return $this;
    }

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

        $endingCharacter   = $this->filterProperties($this->getEndingCharacter());
        $file              = $this->filterProperties($this->getFile());
        $replacements      = $this->getReplacements();
        $startingCharacter = $this->filterProperties($this->getStartingCharacter());


        Pale::run(function() use ($file, $replacements, $endingCharacter, $startingCharacter) {
            $contents = file_get_contents($file);

            foreach ($replacements as $token => $value) {
                $contents = str_replace(
                    $endingCharacter . $token . $startingCharacter,
                    $value,
                    $contents
                );
            }

            return file_put_contents(
                $file,
                $contents
            );
        });

        return $this;
    }

    /**
     * Get the ending character
     *
     * @return string
     */
    public function getEndingCharacter()
    {
        return $this->endingCharacter;
    }

    /**
     * Get the target file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get the replacements
     *
     * @return array
     */
    public function getReplacements()
    {
        return $this->replacements;
    }

    /**
     * Get the starting character
     *
     * @return string
     */
    public function getStartingCharacter()
    {
        return $this->startingCharacter;
    }

    /**
     * Set the ending character
     *
     * @param string $endingCharacter
     * @return TokenFilter
     */
    public function setEndingCharacter($endingCharacter)
    {
        $this->endingCharacter = $endingCharacter;
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
        $this->file = $file;
        return $this;
    }

    /**
     * Set the starting character
     *
     * @param string $startingCharacter
     * @return TokenFilter
     */
    public function setStartingCharacter($startingCharacter)
    {
        $this->startingCharacter = $startingCharacter;
        return $this;
    }

}
