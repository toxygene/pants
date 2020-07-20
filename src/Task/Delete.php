<?php
/**
 * Pants
 *
 * Copyright (c) 2011-2018, Justin Hendrickson
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
 *
 * @author Justin Hendrickson <justin.hendrickson@gmail.com>
 */

declare(strict_types=1);

namespace Pants\Task;

use FilesystemIterator;
use JMS\Serializer\Annotation as JMS;
use Pants\ContextInterface;
use Pants\FilesInterface;
use Pants\Task\Exception\MissingPropertyException;
use Pants\Task\Exception\TaskException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileObject;

/**
 * Delete file(s) task
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Task
 * @todo add path back (see Move)
 */
class Delete implements TaskInterface
{
    /**
     * Files to delete
     *
     * @JMS\Expose()
     * @JMS\SerializedName("files")
     * @JMS\Type("Pants\Fileset\FilesetInterface")
     * @JMS\XmlElement(cdata=false)
     *
     * @var FilesInterface
     */
    protected $files;

    /**
     * Follow symlinks
     *
     * @JMS\Expose()
     * @JMS\SerializedName("follow-symlinks")
     * @JMS\Type("boolean")
     * @JMS\XmlElement(cdata=false)
     *
     * @var bool
     */
    protected $followSymlinks;

    /**
     * Recursive flag
     *
     * @JMS\Expose()
     * @JMS\SerializedName("recursive")
     * @JMS\Type("boolean")
     * @JMS\XmlElement(cdata=false)
     *
     * @var bool
     */
    private $recursive;

    /**
     * Constructor
     *
     * @param FilesInterface $files
     * @param bool $recursive
     * @param bool $followSymlinks
     */
    public function __construct(
        FilesInterface $files,
        bool $recursive = false,
        bool $followSymlinks = false
    )
    {
        $this->files = $files;
        $this->followSymlinks = $followSymlinks;
        $this->recursive = $recursive;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        if (empty($this->files)) {
            throw new MissingPropertyException(
                'path',
                $context->getCurrentTarget(),
                $this
            );
        }

        if (empty($this->files)) {
            foreach ($this->files as $file) {
                $this->delete(
                    $file,
                    $this->recursive
                );
            }
        }

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

            if ($this->followSymlinks) {
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
