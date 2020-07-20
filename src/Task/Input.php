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

use JMS\Serializer\Annotation as JMS;
use Pants\BuildException;
use Pants\ContextInterface;
use Pants\Task\Exception\MissingPropertyException;
use Pants\Task\Exception\TaskException;

/**
 * Read input task
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Task
 */
class Input implements TaskInterface
{
    /**
     * Default value
     *
     * @JMS\Expose()
     * @JMS\SerializedName("default-value")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
     */
    protected $defaultValue;

    /**
     * Input stream
     *
     * @JMS\Expose()
     * @JMS\SerializedName("input-stream")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|resource|null
     */
    protected $inputStream;

    /**
     * Message to display
     *
     * @JMS\Expose()
     * @JMS\SerializedName("message")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
     */
    protected $message;

    /**
     * Output stream
     *
     * @JMS\Expose()
     * @JMS\SerializedName("output-stream")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|resource|null
     */
    protected $outputStream;

    /**
     * Prompt character
     *
     * @JMS\Expose()
     * @JMS\SerializedName("prompt-character")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string
     */
    protected $promptCharacter = '?';

    /**
     * Property to set
     *
     * @JMS\Expose()
     * @JMS\SerializedName("property-name")
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string
     */
    protected $propertyName;

    /**
     * Valid arguments
     *
     * @JMS\Expose()
     * @JMS\SerializedName("valid-arg")
     * @JMS\Type("array<string>")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string[]
     */
    protected $validArgs = array();

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        $propertyName = $context->getProperties()
            ->filter($this->propertyName);

        if (empty($propertyName)) {
            throw new MissingPropertyException(
                'property-name',
                $context->getCurrentTarget(),
                $this
            );
        }

        $inputStream = $this->buildStream($this->inputStream);
        $outputStream = $this->buildStream($this->outputStream);

        $message = $context->getProperties()
            ->filter($this->message);

        if (!empty($message)) {
            $context->getLogger()->debug(
                'Writing message "{message}" to output stream',
                [
                    'message' => $message,
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            fwrite($outputStream, $message);
        }

        $validArgs = [];

        if (!empty($validArgs)) {
            foreach ($this->validArgs as $validArg) {
                $validArgs[] = $context->getProperties()
                    ->filter($validArg);
            }

            $validArgsString = '[' . implode('/', $validArgs) . ']';

            $context->getLogger()->debug(
                'Writing valid arguments "{valid_args}" to output stream',
                [
                    'target' => $context->getCurrentTarget()
                        ->getName(),
                    'valid_args' => $validArgsString
                ]
            );

            fwrite($outputStream, ' ' . $validArgsString);
        }

        $promptCharacter = $context->getProperties()
            ->filter($this->promptCharacter);

        $context->getLogger()->debug(
            'Writing prompt character "{prompt_character}" to output stream',
            [
                'prompt_character' => $promptCharacter,
                'target' => $context->getCurrentTarget()
                    ->getName()
            ]
        );

        fwrite($outputStream, $promptCharacter . ' ');

        $value = trim(fgets($inputStream));

        $defaultValue = $context->getProperties()
            ->filter($this->defaultValue);

        if (empty($value) && !empty($defaultValue)) {
            $context->getLogger()->debug(
                'Using default value "{default_value}"',
                [
                    'default_value' => $defaultValue,
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            $value = $defaultValue;
        }

        if (!empty($validArgs) && !in_array($value, $validArgs)) {
            throw new TaskException(
                sprintf(
                    'Value "%s" is not a valid value "%s"',
                    $value,
                    '[' . implode('/', $validArgs) . ']'
                ),
                $context->getCurrentTarget(),
                $this
            );
        }

        $context->getLogger()->debug(
            'Setting property "{property_name}" to value "{value}"',
            [
                'name' => $propertyName,
                'property_value' => $value,
                'target' => $context->getCurrentTarget()
                    ->getName()
            ]
        );

        $context->getProperties()->add($propertyName, $value);

        return $this;
    }

    /**
     * Build a stream
     *
     * @param string|resource|null $stream
     * @return resource
     * @throws TaskException
     */
    private function buildStream($stream)
    {
        if (is_resource($stream)) {
            return $stream;
        }

        switch ($this->inputStream) {
            case 'stdin':
                return STDIN;

            case 'stdout':
                return STDOUT;

            case 'stderr':
                return STDERR;

            default:
                throw new BuildException();
        }
    }
}
