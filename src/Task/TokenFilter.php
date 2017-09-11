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
use Pants\ContextInterface;
use function Pale\run;

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
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string
     */
    protected $endingCharacter = "@";

    /**
     * Target file
     *
     * @JMS\Expose()
     * @JMS\SerializedName("file")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
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
     * @var array|null
     */
    protected $replacements = array();

    /**
     * Starting character
     *
     * @JMS\Expose()
     * @JMS\SerializedName("starting-character")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string
     */
    protected $startingCharacter = "@";

    /**
     * Add a replacement
     *
     * @param string $token
     * @param string $value
     * @return TokenFilter
     */
    public function addReplacement($token, $value)
    {
        $this->replacements[$token] = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        if (null === $this->getFile()) {
            $message = 'File not set';

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

        $endingCharacter = $context->getProperties()
            ->filter($this->getEndingCharacter());

        $file = $context->getProperties()
            ->filter($this->getFile());

        $startingCharacter = $context->getProperties()
            ->filter($this->getStartingCharacter());

        $contents = file_get_contents($file);

        foreach ($this->getReplacements() as $token => $value) {
            $search = $endingCharacter . $token . $startingCharacter;

            $context->getLogger()->debug(
                sprintf(
                    'Replacing search "%s" with value "%s" in contents "%s"',
                    $search,
                    $value,
                    $contents
                )
            );

            $contents = str_replace(
                $search,
                $value,
                $contents
            );
        }

        $context->getLogger()->debug(
            sprintf(
                'Writing contents "%s" to file "%s"',
                $contents,
                $file
            ),
            [
                'target' => $context->getCurrentTarget()
                    ->getName()
            ]
        );

        try {
            run(function () use ($file, $contents) {
                file_put_contents($file, $contents);
            });
        } catch (ErrorException $e) {
            $message = sprintf(
                'Failed writing contents "%s" to file "%s"',
                $contents,
                $file
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
                $this,
                $e
            );
        }

        return $this;
    }

    /**
     * Get the ending character
     *
     * @return string
     */
    public function getEndingCharacter()
    {
        return $this->endingCharacter;
    }

    /**
     * Get the target file
     *
     * @return string|null
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get the replacements
     *
     * @return array
     */
    public function getReplacements()
    {
        return $this->replacements;
    }

    /**
     * Get the starting character
     *
     * @return string
     */
    public function getStartingCharacter()
    {
        return $this->startingCharacter;
    }

    /**
     * Set the ending character
     *
     * @param string $endingCharacter
     * @return self
     */
    public function setEndingCharacter(string $endingCharacter): self
    {
        $this->endingCharacter = $endingCharacter;
        return $this;
    }

    /**
     * Set the target file
     *
     * @param string $file
     * @return self
     */
    public function setFile(string $file): self
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Set the starting character
     *
     * @param string $startingCharacter
     * @return self
     */
    public function setStartingCharacter(string $startingCharacter): self
    {
        $this->startingCharacter = $startingCharacter;
        return $this;
    }
}
