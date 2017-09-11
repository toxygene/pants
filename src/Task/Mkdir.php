<?php
/**
 * Pants
 *
 * Copyright (c) 2017, Justin Hendrickson
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

namespace Pants\Task;

use ErrorException;
use JMS\Serializer\Annotation as JMS;
use function Pale\run;
use Pants\ContextInterface;

/**
 * @JMS\ExclusionPolicy("all")
 */
class Mkdir extends AbstractTaskInterface
{
    /**
     * @JMS\Expose()
     * @JMS\SerializedName("path")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
     */
    private $path;

    /**
     * @JMS\Expose()
     * @JMS\SerializedName("mode")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|int|null
     */
    private $mode;

    /**
     * @JMS\Expose()
     * @JMS\SerializedName("recursive")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("boolean")
     * @JMS\XmlElement(cdata=false)
     *
     * @var boolean|null
     */
    private $recursive;

    /**
     * @JMS\Expose()
     * @JMS\SerializedName("skip-if-path-exists")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("boolean")
     * @JMS\XmlElement(cdata=false)
     *
     * @var boolean|null
     */
    private $skipIfPathExists;

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        if (null === $this->getPath()) {
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

        $path = $context->getProperties()
            ->filter($this->getPath());

        if ($this->getSkipIfPathExists() && file_exists($path)) {
            $message = sprintf(
                'Directory "%s" already exists, skipping creation',
                $path
            );

            $context->getLogger()->info(
                $message,
                [
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            return $this;
        }

        $mode = $context->getProperties()
            ->filter($this->getMode());

        if (is_string($mode)) {
            $mode = octdec($mode);
        }

        $context->getLogger()->debug(
            sprintf(
                'Creating directory "%s" with mode "%s" and recursive "%s"',
                $path,
                $mode,
                $this->getRecursive()
            ),
            [
                'target' => $context->getCurrentTarget()
                    ->getName()
            ]
        );

        try {
            run(function () use ($path, $mode) {
                mkdir($path, $mode, $this->getRecursive());
            });
        } catch (ErrorException $e) {
            $message = sprintf(
                'Could not create directory "%s" with mode "%s" and recursive "%s" because "%s"',
                $path,
                $mode,
                $this->getRecursive(),
                $e->getMessage()
            );

            $context->getLogger()->error(
                $message,
                [
                    'target' => $context->getCurrentTarget()
                ]
            );

            throw new BuildException(
                $message,
                $context->getCurrentTarget(),
                $this,
                $e
            );
        }

        return $this;
    }

    /**
     * Get the mode
     *
     * @return string|int|null
     */
    public function getMode()
    {
        if (null === $this->mode) {
            $this->mode = 0775;
        }

        return $this->mode;
    }

    /**
     * Get the path
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
     * @return boolean
     */
    public function getRecursive()
    {
        return true === $this->recursive;
    }

    /**
     * Get the skip if path exists flag
     *
     * @return boolean
     */
    public function getSkipIfPathExists()
    {
        return true === $this->skipIfPathExists;
    }

    /**
     * Set the mode
     *
     * @param string|int $mode
     * @return self
     */
    public function setMode($mode): self
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * Set the path
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
     * @param boolean $recursive
     * @return self
     */
    public function setRecursive(bool $recursive): self
    {
        $this->recursive = $recursive;
        return $this;
    }

    /**
     * Set the skip if path exists flag
     *
     * @param boolean $skipIfPathExists
     * @return self
     */
    public function setSkipIfPathExists(bool $skipIfPathExists): self
    {
        $this->skipIfPathExists = $skipIfPathExists;
        return $this;
    }
}
