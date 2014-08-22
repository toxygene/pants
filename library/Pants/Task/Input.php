<?php
/**
 * Pants
 *
 * Copyright (c) 2011, Justin Hendrickson
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

use Pants\BuildException;
use Pants\Property\Properties;

/**
 * Read input task
 *
 * @package Pants\Task
 */
class Input implements Task
{

    /**
     * Default value
     *
     * @var string
     */
    protected $defaultValue;
    
    /**
     * Input stream
     *
     * @var resource
     */
    protected $inputStream = STDIN;

    /**
     * Message to display
     *
     * @var string
     */
    protected $message;
    
    /**
     * Output stream
     *
     * @var resource
     */
    protected $outputStream = STDOUT;

    /**
     * Prompt character
     *
     * @var string
     */
    protected $promptCharacter = '?';

    /**
     * Properties
     *
     * @var Propreties
     */
    protected $properties;

    /**
     * Property to set
     *
     * @var string
     */
    protected $propertyName;

    /**
     * Valid arguments
     *
     * @var array
     */
    protected $validArgs = array();

    /**
     * Constructor
     *
     * @param Properties $properties
     */
    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    /**
     * Execute the task
     *
     * @return Input
     */
    public function execute()
    {
        if (!$this->getPropertyName()) {
            throw new BuildException('Property name not set');
        }

        if ($this->getMessage()) {
            fwrite($this->getOutputStream(), $this->properties->filter($this->getMessage()));
        }

        $validArgs = array();

        foreach ($this->getValidArgs() as $validArg) {
            $validArgs[] = $this->properties->filter($validArg);
        }

        if ($validArgs) {
            fwrite($this->getOutputStream(), ' [' . implode('/', $validArgs) . ']');
        }

        fwrite($this->getOutputStream(), $this->properties->filter($this->getPromptCharacter()) . ' ');

        $value = trim(fgets($this->getInputStream()));

        if (trim($value) == '') {
            $value = $this->properties->filter($this->getDefaultValue());
        }

        if ($validArgs && !in_array($value, $validArgs)) {
            throw new BuildException('Invalid argument');
        }

        $this->properties
            ->{$this->getPropertyName()} = $value;

        return $this;
    }

    /**
     * Get the default value
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
    
    /**
     * Get the input stream
     *
     * @return resource
     */
    public function getInputStream()
    {
        return $this->inputStream;
    }

    /**
     * Get the message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * Get the output stream
     *
     * @return resource
     */
    public function getOutputStream()
    {
        return $this->outputStream;
    }

    /**
     * Get the prompt character
     *
     * @return string
     */
    public function getPromptCharacter()
    {
        return $this->promptCharacter;
    }

    /**
     * Get the property name
     *
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * Get the valid arguements
     *
     * @return string
     */
    public function getValidArgs()
    {
        return $this->validArgs;
    }

    /**
     * Set the default value
     *
     * @param string $defaultValue
     * @return Input
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }
    
    /**
     * Set the input stream
     *
     * @param resource $inputStream
     * @return Input
     */
    public function setInputStream($inputStream)
    {
        $this->inputStream = $inputStream;
        return $this;
    }

    /**
     * Set the message
     *
     * @param string $message
     * @return Input
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Set the prompt character
     *
     * @param string $promptCharacter
     * @return Input
     */
    public function setPromptCharacter($promptCharacter)
    {
        $this->promptCharacter = $promptCharacter;
        return $this;
    }
    
    /** 
     * Set the output stream
     *
     * @param resource $outputStream
     * @return Input
     */
    public function setOutputStream($outputStream)
    {
        $this->outputStream = $outputStream;
        return $this;
    }

    /**
     * Set the property name
     *
     * @param string $propertyName
     * @return Input
     */
    public function setPropertyName($propertyName)
    {
        $this->propertyName = $propertyName;
        return $this;
    }
    
    /**
     * Set the valid arguments
     *
     * @param array $validArgs
     * @return Input
     */
    public function setValidArgs($validArgs)
    {
        $this->validArgs = $validArgs;
        return $this;
    }

}
