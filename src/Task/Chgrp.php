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

namespace Pants\Task;

use ErrorException;
use JMS\Serializer\Annotation as JMS;
use Pants\ContextInterface;
use Traversable;
use function Pale\run;

/**
 * Change files group task
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Task
 */
class Chgrp implements TaskInterface
{

    /**
     * Target files
     *
     * @var array|null
     */
    protected $files;

    /**
     * Group to set
     *
     * @JMS\Expose()
     * @JMS\SerializedName("group")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
     */
    protected $group;

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        if (null === $this->getFiles()) {
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

        if (null === $this->getGroup()) {
            $message = 'Group not set';

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

        $group = $context->getProperties()
            ->filter($this->getGroup());

        foreach ($this->getFiles() as $file) {
            $file = $context->getProperties()
                ->filter($file);

            $context->getLogger()->debug(
                sprintf(
                    'Setting group "%s" on file "%s"',
                    $group,
                    $file
                ),
                [
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            try {
                run(function () use ($file, $group) {
                    chgrp($file, (int)$group);
                });
            } catch (ErrorException $e) {
                $message = sprintf(
                    'Could not set group "%s" on file "%s" because "%s"',
                    $group,
                    $file,
                    $e->getMessage()
                );

                $context->getLogger()->error(
                    $message,
                    [
                        'target' => $context->getCurrentTarget()
                            ->getName(),
                        'reason' => $e->getMessage()
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
     * @return array|null
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Get the group
     *
     * @return string|null
     */
    public function getGroup()
    {
        return $this->group;
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
     * Set the group
     *
     * @param string $group
     * @return self
     */
    public function setGroup(string $group): self
    {
        $this->group = $group;
        return $this;
    }
}
