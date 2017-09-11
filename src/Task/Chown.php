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

use ErrorException;
use JMS\Serializer\Annotation as JMS;
use function Pale\run;
use Pants\ContextInterface;
use Traversable;

/**
 * Change file(s) owner
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Task
 */
class Chown implements TaskInterface
{

    /**
     * Target files
     *
     * @var Traversable|null
     */
    protected $files;

    /**
     * Owner to set
     *
     * @JMS\Expose()
     * @JMS\SerializedName("owner")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string
     */
    protected $owner;

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        if (!$this->getFiles()) {
            $message = 'Files not set';

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

        if (!$this->getOwner()) {
            $message = 'Owner not set';

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

        $owner = $context->getProperties()
            ->filter($this->getOwner());

        foreach ($this->getFiles() as $file) {
            $file = $context->getProperties()
                ->filter($file);

            $context->getLogger()->debug(
                sprintf(
                    'Setting owner "%owner" on file "%file"',
                    $owner,
                    $file
                ),
                [
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            try {
                run(function() use ($file, $owner) {
                    chown($file, $owner);
                });
            } catch (ErrorException $e) {
                $message = sprintf(
                    'Could not set owner "%s" on file "%s" because "%s"',
                    $owner,
                    $file,
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
        }

        return $this;
    }

    /**
     * Get the target files
     *
     * @return Traversable|null
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Get the owner
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set the target file
     *
     * @param string $file
     * @return self
     */
    public function setFile(string $file): self
    {
        $this->files = [$file];
        return $this;
    }

    /**
     * Set the target files
     *
     * @param Traversable $files
     * @return self
     */
    public function setFiles(Traversable $files): self
    {
        $this->files = $files;
        return $this;
    }

    /**
     * Set the owner
     *
     * @param string $owner
     * @return self
     */
    public function setOwner(string $owner): self
    {
        $this->owner = $owner;
        return $this;
    }
}
