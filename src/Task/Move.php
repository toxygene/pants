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
use Pants\Fileset\Fileset;
use function Pale\run;

/**
 * Move file task
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Task
 */
class Move implements TaskInterface
{

    /**
     * Fileset to move
     *
     * @JMS\Expose()
     * @JMS\SerializedName("fileset")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("Pants\Fileset\Fileset")
     * @JMS\XmlElement(cdata=false)
     *
     * @var Fileset|null
     */
    protected $fileSet;

    /**
     * Target file
     *
     * @JMS\Expose()
     * @JMS\SerializedName("source")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
     */
    protected $source;

    /**
     * Destination file
     *
     * @JMS\Expose()
     * @JMS\SerializedName("destination")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
     */
    protected $destination;

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        if (null === $this->getSource() && null === $this->getFileset()) {
            $message = 'Source not set';

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

        if (null === $this->getDestination()) {
            $message = 'Directory not set';

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

        $destination = $context->getProperties()
            ->filter($this->getDestination());

        $source = $context->getProperties()
            ->filter($this->getSource());

        if (null !== $this->getSource()) {
            if (!is_dir($source) && is_dir($destination)) {
                $dest = $destination . DIRECTORY_SEPARATOR . basename($source);
            } else {
                $dest = $destination;
            }

            $context->getLogger()->debug(
                sprintf(
                    'Renaming source "%s" to destination "%s"',
                    $source,
                    $dest
                ),
                [
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            $this->rename($source, $dest, $context);
        }

        if (null !== $this->getFileset()) {
            if (is_file($destination)) {
                $message = sprintf(
                    'Cannot move a fileset to file "%s"',
                    $destination
                );

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

            foreach ($this->getFileset()->getIterator($context) as $source) {
                $dest = $destination . DIRECTORY_SEPARATOR . basename($source);

                $context->getLogger()->debug(
                    sprintf(
                        'Renaming source "%s" to destination "%s"',
                        $source,
                        $dest
                    ),
                    [
                        'target' => $context->getCurrentTarget()
                            ->getName()
                    ]
                );

                $this->rename($source, $dest, $context);
            }
        }

        return $this;
    }

    /**
     * Get the destination
     *
     * @return string|null
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Get the fileset
     *
     * @return Fileset|null
     */
    public function getFileset()
    {
        return $this->fileSet;
    }

    /**
     * Get the target file
     *
     * @return string|null
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the destination file
     *
     * @param string $destination
     * @return self
     */
    public function setDestination(string $destination): self
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * Set the fileset
     *
     * @param Fileset $fileSet
     * @return self
     */
    public function setFileSet(Fileset $fileSet)
    {
        $this->fileSet = $fileSet;
        return $this;
    }

    /**
     * Set the target file
     *
     * @param string $source
     * @return self
     */
    public function setSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Rename a source
     *
     * @param string $source
     * @param string $destination
     * @param ContextInterface $context
     * @throws BuildException
     */
    protected function rename($source, $destination, ContextInterface $context)
    {
        try {
            run(function () use ($source, $destination) {
                rename($source, $destination);
            });
        } catch (ErrorException $e) {
            $message = sprintf(
                'Could not rename source "%s" to destination "%s" because "%s"',
                $source,
                $destination,
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
}
