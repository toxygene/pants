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

use Pants\BuildException;

/**
 * Read input task
 *
 * @package Pants\Task
 */
class Input
{

    /**
     * Property to set
     *
     * @var string
     */
    protected $propertyName;

    /**
     * Default value
     *
     * @var string
     */
    protected $defaultValue;

    /**
     * Message to display
     *
     * @var string
     */
    protected $message;

    /**
     * Prompt character
     *
     * @var string
     */
    protected $promptCharacter = "?";

    /**
     * Valid arguments
     *
     * @var array
     */
    protected $validArgs = array();

    /**
     * Execute the task
     *
     * @return Input
     */
    public function execute()
    {
        if (!$this->getPropertyName()) {
            throw new BuildException("Property name not set");
        }

        if ($this->getMessage()) {
            echo $this->filterProperties($this->getMessages());
        }

        if ($this->getValidArgs()) {
            echo " [" . implode("/", $this->getValidArgs()) . "]";
        }

        echo $this->filterProperties($this->getPromptCharacter()) . " ";

        $value = fgets(STDIN);

        if (trim($value) == "") {
            $value = $this->filterProperties($this->getDefaultValue());
        }

        $this->getProject()
             ->getProperties()
             ->{$this->getPropertyName()} = $value;

        return $this;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getPromptCharacter()
    {
        return $this->promptCharacter;
    }

    public function getPropertyName()
    {
        return $this->propertyName;
    }

    public function getValidArgs()
    {
        return $this->validArgs;
    }

    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function setPromptCharacter($promptCharacter)
    {
        $this->promptCharacter = $promptCharacter;
        return $this;
    }

    public function setPropertyName($propertyName)
    {
        $this->propertyName = $propertyName;
        return $this;
    }

}
