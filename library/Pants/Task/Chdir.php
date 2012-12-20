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
 * Change the current working directory task
 *
 * @package Pants\Task
 */
class Chdir extends AbstractTask
{

    /**
     * Target directory
     *
     * @var string
     */
    protected $directory;

    /**
     * Chgrp a file
     *
     * @param string $dir
     * @return boolean
     */
    protected function chdir($dir)
    {
        return $this->run(function() use ($dir) {
            return chdir($dir);
        });
    }

    /**
     * Execute the task
     *
     * @return Chgrp
     * @throws BuildException
     * @return self
     */
    public function execute()
    {
        if (!$this->getDirectory()) {
            throw new BuildException("Directory is not set");
        }

        $this->chdir($this->getDirectory());

        return $this;
    }

    /**
     * Get the target directory
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Set the target directory
     *
     * @param string $directory
     * @return self
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

}
