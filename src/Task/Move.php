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

use ErrorException;
use JMS\Serializer\Annotation as JMS;
use Pants\ContextInterface;
use Pants\Files;
use function Pale\run;
use Pants\FilesInterface;
use Pants\Task\Exception\MissingPropertyException;
use Pants\Task\Exception\TaskException;

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
     * @JMS\Type("Pants\Fileset\FilesetInterface")
     * @JMS\XmlElement(cdata=false)
     *
     * @var FilesInterface|null
     */
    protected $fileset;

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
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string
     */
    protected $destination;

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        $source = $context->getProperties()
            ->filter($this->source);

        if (empty($source) && empty($this->fileset)) {
            throw new MissingPropertyException(
                'source',
                $context->getCurrentTarget(),
                $this
            );
        }

        $destination = $context->getProperties()
            ->filter($this->destination);

        if (empty($destination)) {
            throw new MissingPropertyException(
                'destination',
                $context->getCurrentTarget(),
                $this
            );
        }

        if (!empty($source)) {
            if (!is_dir($source) && is_dir($destination)) {
                $dest = $destination . DIRECTORY_SEPARATOR . basename($source);
            } else {
                $dest = $destination;
            }

            $context->getLogger()->debug(
                'Renaming source "{source}" to destination "{destination}"',
                [
                    'destination' => $destination,
                    'source' => $source,
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            $this->rename($source, $dest, $context);
        }

        if (!empty($this->fileset)) {
            if (is_file($destination)) {
                throw new TaskException(
                    sprintf(
                        'Cannot move a fileset to file "%s"',
                        $destination
                    ),
                    $context->getCurrentTarget(),
                    $this
                );
            }

            foreach ($this->fileset->getIterator($context) as $source) {
                $dest = $destination . DIRECTORY_SEPARATOR . basename($source);

                $context->getLogger()->debug(
                    'Renaming source "{source}" to destination "{destination}"',
                    [
                        'destination' => $dest,
                        'source' => $source,
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
     * Rename a source
     *
     * @param string $source
     * @param string $destination
     * @param ContextInterface $context
     * @throws TaskException
     */
    protected function rename($source, $destination, ContextInterface $context)
    {
        try {
            run(function () use ($source, $destination) {
                rename($source, $destination);
            });
        } catch (ErrorException $e) {
            throw new TaskException(
                sprintf(
                    'Could not rename source "%s" to destination "%s" because "%s"',
                    $source,
                    $destination,
                    $e->getMessage()
                ),
                $context->getCurrentTarget(),
                $this,
                $e
            );
        }
    }
}
