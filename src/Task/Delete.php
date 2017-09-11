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

use FilesystemIterator;
use JMS\Serializer\Annotation as JMS;
use Pants\ContextInterface;
use Pants\Fileset\FilesetInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileObject;

/**
 * Delete file(s) task
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Task
 */
class Delete implements TaskInterface
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
     * @var FilesetInterface|null
     */
    protected $fileset;

    /**
     * Follow symlinks
     *
     * @JMS\Expose()
     * @JMS\SerializedName("follow-symlinks")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("boolean")
     * @JMS\XmlElement(cdata=false)
     *
     * @var bool|null
     */
    protected $followSymlinks;

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
     * @JMS\SerializedName("recursive")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("boolean")
     * @JMS\XmlElement(cdata=false)
     *
     * @var bool|null
     */
    private $recursive;

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        // todo support for follow symlinks

        if (null === $this->getPath() && null === $this->getFileset()) {
            $message = 'Path not set';

            $context->getLogger()->error(
                $message,
                [
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            throw new BuildException(
                $message,
                $context->getCurrentTarget(),
                $this
            );
        }

        if (null !== $this->getPath()) {
            $path = $context->getProperties()
                ->filter($this->getPath());

            $this->delete($path, $this->getRecursive());
        }

        if (null !== $this->getFileset()) {
            foreach ($this->getFileset()->getIterator($context) as $path) {
                $this->delete($path, $this->getRecursive());
            }
        }

        return $this;
    }

    /**
     * Get the fileset to apply the delete to
     *
     * @return FilesetInterface|null
     */
    public function getFileset()
    {
        return $this->fileset;
    }

    /**
     * Get the follow symlinks flag
     *
     * @return bool
     */
    public function getFollowSymlinks()
    {
        return true === $this->followSymlinks;
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
     * @param FilesetInterface $fileset
     * @return self
     */
    public function setFileset(FilesetInterface $fileset): self
    {
        $this->fileset = $fileset;
        return $this;
    }

    /**
     * Set the follow symlinks flag
     *
     * @param bool $followSymlinks
     * @return self
     */
    public function setFollowSymlinks(bool $followSymlinks): self
    {
        $this->followSymlinks = $followSymlinks;
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
     * @param bool $recursive
     * @return bool
     */
    protected function delete($path, bool $recursive)
    {
        // todo add support for skipping if the path doesn't exist
        // todo logging

        if ($recursive) {
            $flags = FilesystemIterator::KEY_AS_PATHNAME |
                FilesystemIterator::CURRENT_AS_FILEINFO |
                FilesystemIterator::SKIP_DOTS;

            if ($this->getFollowSymlinks()) {
                $flags |= FilesystemIterator::FOLLOW_SYMLINKS;
            }

            /** @var RecursiveIteratorIterator|SplFileObject[] $iterator */
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, $flags),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($iterator as $item) {
                if ($item->isDir()) {
                    if (!rmdir($item)) {
                        return false;
                    }
                } else {
                    if (!unlink($item)) {
                        return false;
                    }
                }
            }

            return true;
        } else {
            if (is_dir($path)) {
                return rmdir($path);
            } else {
                return unlink($path);
            }
        }
    }

}
