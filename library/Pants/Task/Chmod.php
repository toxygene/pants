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

use Pants\BuildException;

/**
 * Change file(s) permissions
 *
 * @package Pants
 * @subpackage Task
 */
class Chmod extends AbstractTask
{

    /**
     * Target file
     * @var string
     */
    protected $_file;

    /**
     * Mode to set
     * @var string
     */
    protected $_mode;

    /**
     * Execute the task
     *
     * @return Chmod
     * @throws BuildException
     */
    public function execute()
    {
        if (!$this->getFile()) {
            throw new BuildException("File is not set");
        }

        if (!$this->getMode()) {
            throw new BuildException("Mode is not set");
        }

        $file = $this->filterProperties($this->getFile());
        $mode = $this->filterProperties($this->getMode());

        $this->_run(function() use ($file, $mode) {
            return chmod($file, $mode);
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
     * Get the mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->_mode;
    }

    /**
     * Set the target file
     *
     * @param string $file
     * @return Chmod
     */
    public function setFile($file)
    {
        $this->_file = $file;
        return $this;
    }

    /**
     * Set the mode
     *
     * @param string $mode
     * @return Chmod
     */
    public function setMode($mode)
    {
        $this->_mode = $mode;
        return $this;
    }

}
