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

use JMS\Serializer\Annotation as JMS;
use Pants\ContextInterface;

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
     * @var string|null
     */
    protected $promptCharacter = '?';

    /**
     * Property to set
     *
     * @JMS\Expose()
     * @JMS\SerializedName("property-name")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
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
        if (!$this->getPropertyName()) {
            $message = 'Property name not set';

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

        $inputStream = $this->buildStream(
            $this->getInputStream()
        );

        $outputStream = $this->buildStream(
            $this->getOutputStream()
        );

        if ($this->getMessage()) {
            $message = $context->getProperties()
                ->filter($this->getMessage());

            $context->getLogger()->debug(
                sprintf(
                    'Writing message "%s" to output stream',
                    $message
                ),
                [
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            fwrite($outputStream, $message);
        }

        $validArgs = array();

        foreach ($this->getValidArgs() as $validArg) {
            $validArgs[] = $context->getProperties()
                ->filter($validArg);
        }

        if ($validArgs) {
            $validArgsString = '[' . implode('/', $validArgs) . ']';

            $context->getLogger()->debug(
                sprintf(
                    'Writing valid arguments "%s" to output stream',
                    $validArgsString
                ),
                [
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            fwrite($outputStream, ' ' . $validArgsString);
        }

        $promptCharacter = $context->getProperties()
            ->filter($this->getPromptCharacter());

        $context->getLogger()->debug(
            sprintf(
                'Writing prompt character "%s" to output stream',
                $promptCharacter
            ),
            [
                'target' => $context->getCurrentTarget()
                    ->getName()
            ]
        );

        fwrite($outputStream, $promptCharacter . ' ');

        $value = trim(fgets($inputStream));

        if (trim($value) == '' && null !== $this->getDefaultValue()) {
            $defaultValue = $context->getProperties()
                ->filter($this->getDefaultValue());

            $context->getLogger()->debug(
                sprintf(
                    'Using default value "%s"',
                    $defaultValue
                ),
                [
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            $value = $defaultValue;
        }

        if ($validArgs && !in_array($value, $validArgs)) {
            $message = sprintf(
                'Value "%s" is not a valid value "%s"',
                $value,
                '[' . implode('/', $validArgs) . ']'
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

        $propertyName = $this->getPropertyName();

        $context->getLogger()->debug(
            sprintf(
                'Setting property "%s" to value "%s"',
                $propertyName,
                $value
            ),
            [
                'target' => $context->getCurrentTarget()
                    ->getName()
            ]
        );

        $context->getProperties()->add($propertyName, $value);

        return $this;
    }

    /**
     * Get the default value
     *
     * @return string|null
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Get the input stream
     *
     * @return string|resource|null
     */
    public function getInputStream()
    {
        return $this->inputStream;
    }

    /**
     * Get the message
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get the output stream
     *
     * @return string|resource|null
     */
    public function getOutputStream()
    {
        return $this->outputStream;
    }

    /**
     * Get the prompt character
     *
     * @return string|null
     */
    public function getPromptCharacter()
    {
        return $this->promptCharacter;
    }

    /**
     * Get the property name
     *
     * @return string|null
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * Get the valid arguments
     *
     * @return string[]
     */
    public function getValidArgs()
    {
        return $this->validArgs;
    }

    /**
     * Set the default value
     *
     * @param string $defaultValue
     * @return self
     */
    public function setDefaultValue(string $defaultValue): self
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * Set the input stream
     *
     * @param string|resource $inputStream
     * @return self
     */
    public function setInputStream($inputStream): self
    {
        $this->inputStream = $inputStream;
        return $this;
    }

    /**
     * Set the message
     *
     * @param string $message
     * @return self
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Set the output stream
     *
     * @param string|resource $outputStream
     * @return self
     */
    public function setOutputStream($outputStream): self
    {
        $this->outputStream = $outputStream;
        return $this;
    }

    /**
     * Set the prompt character
     *
     * @param string $promptCharacter
     * @return self
     */
    public function setPromptCharacter(string $promptCharacter): self
    {
        $this->promptCharacter = $promptCharacter;
        return $this;
    }

    /**
     * Set the property name
     *
     * @param string $propertyName
     * @return self
     */
    public function setPropertyName(string $propertyName): self
    {
        $this->propertyName = $propertyName;
        return $this;
    }
    
    /**
     * Set the valid arguments
     *
     * @param array $validArgs
     * @return self
     */
    public function setValidArgs(array $validArgs): self
    {
        $this->validArgs = $validArgs;
        return $this;
    }

    /**
     * Build a stream
     *
     * @param string|resource|null $stream
     * @return resource
     * @throws BuildException
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
                throw new BuildException('');
        }
    }
}
