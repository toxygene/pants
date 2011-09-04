<?php
/**
 *
 */

namespace Pants\Task;

use Pants\BuildException;

/**
 *
 */
class Input
{

    protected $_propertyName;

    protected $_defaultValue;

    protected $_message;

    protected $_promptCharacter = "?";

    protected $_validArgs;

    public function execute()
    {
        if (!$this->getPropertyName()) {
            throw new BuildException("Property name not set");
        }

        if ($message) {
            echo $message;
        }

        echo $this->getPromptCharacter();

        if ($this->getValidArgs()) {
            echo " [" . implode("/", $this->getValidArgs()) . "]";
        }

        echo " ";

        $value = fgets(STDIN);

        if (trim($value) == "") {
            $value = $this->getDefaultValue();
        }

        $this->getProject()->getProperties()->{$this->getPropertyName()} = $value;

        return $this;
    }

    public function getDefaultValue()
    {
        return $this->_defaultValue;
    }

    public function getMessage()
    {
        return $this->_message;
    }

    public function getPromptCharacter()
    {
        return $this->_promptCharacter;
    }

    public function getPropertyName()
    {
        return $this->_propertyName;
    }

    public function getValidArgs()
    {
        return $this->_validArgs;
    }

    public function setDefaultValue($defaultValue)
    {
        $this->_defaultValue = $defaultValue;
        return $this;
    }

    public function setMessage($message)
    {
        $this->_message = $message;
        return $this;
    }

    public function setPromptCharacter($promptCharacter)
    {
        $this->_promptCharacter = $promptCharacter;
        return $this;
    }

    public function setPropertyName($propertyName)
    {
        $this->_propertyName = $propertyName;
        return $this;
    }

}
