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
use Pants\BuildException;
use Pants\ContextInterface;
use function Pale\run;
use Pants\Task\Exception\MissingPropertyException;
use Pants\Task\Exception\TaskException;

/**
 * Replace tokens in file(s) task
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Task
 */
class TokenFilter implements TaskInterface
{
    /**
     * Ending character
     *
     * @JMS\Expose()
     * @JMS\SerializedName("ending-character")
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string
     */
    protected $endingCharacter;

    /**
     * Target file
     *
     * @JMS\Expose()
     * @JMS\SerializedName("file")
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string
     */
    protected $file;

    /**
     * Replacements
     *
     * @JMS\Expose()
     * @JMS\SerializedName("replacement")
     * @JMS\Type("array<string>")
     * @JMS\XmlElement(cdata=false)
     *
     * @var array
     */
    protected $replacements = array();

    /**
     * Starting character
     *
     * @JMS\Expose()
     * @JMS\SerializedName("starting-character")
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string
     */
    protected $startingCharacter;

    /**
     * Constructor
     *
     * @param string $file
     * @param array $replacements
     * @param string $startingCharacter
     * @param string $endingCharacter
     */
    public function __construct(
        string $file,
        array $replacements = [],
        string $startingCharacter = '@',
        string $endingCharacter = '@'
    )
    {
        $this->file = $file;
        $this->replacements = $replacements;
        $this->startingCharacter = $startingCharacter;
        $this->endingCharacter = $endingCharacter;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        $file = $context->getProperties()
            ->filter($this->file);

        if (empty($file)) {
            throw new MissingPropertyException(
                'file',
                $context->getCurrentTarget(),
                $this
            );
        }

        $startingCharacter = $context->getProperties()
            ->filter($this->startingCharacter);

        if (empty($startingCharacter)) {
            throw new MissingPropertyException(
                'starting-character',
                $context->getCurrentTarget(),
                $this
            );
        }

        $endingCharacter = $context->getProperties()
            ->filter($this->endingCharacter);

        if (empty($endingCharacter)) {
            throw new MissingPropertyException(
                'ending-character',
                $context->getCurrentTarget(),
                $this
            );
        }

        if (!file_exists($file)) {
            throw new BuildException(
                sprintf(
                    'File "%s" does not exist',
                    $file
                )
            );
        }

        $contents = file_get_contents($file);

        foreach ($this->replacements as $token => $value) {
            $search = $endingCharacter . $token . $startingCharacter;

            $context->getLogger()->debug(
                'Replacing search "{search}" with value "{value}" in contents "{contents}"',
                [
                    'search' => $search,
                    'value' => $value,
                    'contents' => $contents
                ]
            );

            $contents = str_replace(
                $search,
                $value,
                $contents
            );
        }

        $context->getLogger()->debug(
            'Writing contents "{contents}" to file "{file}"',
            [
                'contents' => $contents,
                'file' => $file,
                'target' => $context->getCurrentTarget()
                    ->getName()
            ]
        );

        try {
            run(function () use ($file, $contents) {
                file_put_contents($file, $contents);
            });
        } catch (ErrorException $e) {
            throw new TaskException(
                sprintf(
                    'Failed writing contents "%s" to file "%s"',
                    $contents,
                    $file
                ),
                $context->getCurrentTarget(),
                $this,
                $e
            );
        }

        return $this;
    }
}
