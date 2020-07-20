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
use Pants\FilesInterface;
use Pants\Task\Exception\MissingPropertyException;
use Pants\Task\Exception\TaskException;
use Traversable;
use function Pale\run;

/**
 * Change files permissions
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Task
 */
class Chmod implements TaskInterface
{

    /**
     * Target files
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
     * Mode to set
     *
     * @JMS\Expose()
     * @JMS\SerializedName("mode")
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|int|null
     */
    protected $mode;

    /**
     * Constructor
     *
     * @param FilesInterface $files
     * @param string|int $mode
     */
    public function __construct(
        FilesInterface $files,
        $mode
    )
    {
        $this->files = $files;
        $this->mode = $mode;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        if (empty($this->files)) {
            throw new MissingPropertyException(
                'files',
                $context->getCurrentTarget(),
                $this
            );
        }

        if (empty($this->mode)) {
            throw new MissingPropertyException(
                'mode',
                $context->getCurrentTarget(),
                $this
            );
        }

        $mode = $context->getProperties()
            ->filter($this->mode);

        if (is_string($mode)) {
            $mode = octdec($mode);
        }

        foreach ($this->files->getIterator($context) as $file) {
            $file = $context->getProperties()
                ->filter($file);

            $context->getLogger()->debug(
                'Setting mode "%s" on file "%s"',
                [
                    'file' => $file,
                    'mode' => decoct($mode),
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            try {
                run(function () use ($file, $mode) {
                    chmod($file, $mode);
                });
            } catch (ErrorException $e) {
                throw new TaskException(
                    sprintf(
                        'Could not set mode "%s" on file "%s" because "%s"',
                        decoct($mode),
                        $file,
                        $e->getMessage()
                    ),
                    $context->getCurrentTarget(),
                    $this,
                    $e
                );
            }
        }

        return $this;
    }
}
