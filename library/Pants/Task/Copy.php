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

use Pants\BuildException,
    Pants\Task\AbstractFileSystemTask,
    Pile\Exception as PileException;

/**
 *
 */
class Copy extends AbstractFileSystemTask
{

    /**
     * Target file
     * @var string
     */
    protected $_file;

    /**
     * Destination file
     * @var string
     */
    protected $_destination;

    /**
     * Get the destination
     *
     * @return string
     */
    public function getDestination()
    {
        return $this->_destination;
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
     * Execute the task
     *
     * @return Copy
     */
    public function execute()
    {
        $file        = $this->filterProperties($this->getFile());
        $destination = $this->filterProperties($this->getDestination());

        try {
            $this->getFileSystem()->copy($file, $destination);
        } catch (PileException $e) {
            throw new BuildException("Could not copy '{$file}' to '{$destination}'", null, $e);
        }

        return $this;
    }

    /**
     * Set the destination file
     *
     * @param string $destination
     * @return Copy
     */
    public function setDestination($destination)
    {
        $this->_destination = $destination;
        return $this;
    }

    /**
     * Set the target file
     *
     * @param string $file
     * @return Copy
     */
    public function setFile($file)
    {
        $this->_file = $file;
        return $this;
    }

}
