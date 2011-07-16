<?php
/**
 *
 */

namespace Pants\Task;

use Pants\Task\AbstractTask;

/**
 *
 */
class Property extends AbstractTask
{

    /**
     * Name
     * @var string
     */
    protected $_name;

    /**
     * Value
     * @var string
     */
    protected $_value;

    /**
     * Set the properties
     *
     * @return Property
     */
    public function execute()
    {
        $properties = $this->getProject()
                           ->getProperties();

        $properties->{$this->getName()} = $this->getValue();

        return $this;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Get the value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set the name
     *
     * @param string $name
     * @return Property
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Set the value
     *
     * @param string $value
     * @return Property
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

}
