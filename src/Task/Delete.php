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

use JMS\Serializer\Annotation as JMS;
use Pants\BuildException;
use Pants\Project;

/**
 * Delete file(s) task
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Task
 */
class Delete implements Task
{

    /**
     * Fileset to delete
     *
     * @JMS\Expose()
     * @JMS\SerializedName("fileset")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("Pants\Fileset\Fileset")
     * @JMS\XmlElement(cdata=false)
     *
     * @var Fileset|null
     */
    protected $fileset;

    /**
     * Path to delete
     *
     * @JMS\Expose()
     * @JMS\SerializedName("path")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
     */
    protected $path;

    /**
     * Recursive flag
     *
     * @JMS\Expose()
     * @JMS\SerializedName("boolean")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var bool|null
     */
    private $recursive;

    /**
     * {@inheritdoc}
     */
    public function execute(Project $project): Task
    {
        if (null === $this->getPath() && null === $this->getFileset()) {
            throw new BuildException('Path not set');
        }

        if (null !== $this->getPath()) {
            $path = $project->getProperties()
                ->filter($this->getPath());

            if (!$this->delete($path)) {
                throw new BuildException();
            }
        }

        if (null !== $this->getFileset()) {
            foreach ($this->getFileset() as $path) {
                if (!$this->delete($path)) {
                    throw new BuildException();
                }
            }
        }

        return $this;
    }

    /**
     * Get the fileset to apply the delete to
     *
     * @return Fileset|null
     */
    public function getFileset()
    {
        return $this->fileset;
    }

    /**
     * Get the path to apply the delete to
     *
     * @return string|null
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the recursive flag
     *
     * If the path is a directory, this determines if the unlink should be recursive.
     *
     * @return bool
     */
    public function getRecursive()
    {
        return true === $this->recursive;
    }

    /**
     * Set the fileset to apply the delete to
     *
     * @param Fileset $fileset
     * @return self
     */
    public function setFileset(Fileset $fileset): self
    {
        $this->fileset = $fileset;
        return $this;
    }

    /**
     * Set the path to apply the delete to
     *
     * @param string $path
     * @return self
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Set the recursive flag
     *
     * @see getRecursive
     *
     * @param bool $recursive
     * @return self
     */
    public function setRecursive(bool $recursive): self
    {
        $this->recursive = $recursive;
        return $this;
    }

    /**
     * Unlink a path
     *
     * @param string $path
     * @return bool
     */
    protected function delete($path)
    {
        if (is_dir($path)) {
            return rmdir($path);
        } else {
            return unlink($path);
        }
    }

}
