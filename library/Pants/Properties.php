<?php
/**
 *
 */

namespace Pants;

use InvalidArgumentException,
    Pants\Properties\PropertyNameCycleException;

/**
 *
 */
class Properties
{

    /**
     * Items
     * @var array
     */
    protected $_items = array();

    /**
     * Get a property
     *
     * @param string $name
     * @return string
     * @throws InvalidArgumentException
     */
    public function __get($name)
    {
        $filteredName = $this->filter($name);

        if (!isset($this->$filteredName)) {
            throw new InvalidArgumentException("There is no property with a name of '{$name}'");
        }

        return $this->_items[$filteredName];
    }

    /**
     * Check if a property exists
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->_items[$this->filter($name)]);
    }

    /**
     * Set a property
     *
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value)
    {
        $this->_items[$name] = $value;
    }

    /**
     * Unset a property
     *
     * @param string $name
     */
    public function __unset($name)
    {
        $filteredName = $this->filter($name);

        if (!isset($this->$filteredName)) {
            throw new InvalidArgumentException("There is no property with a name of '{$name}'");
        }

        unset($this->_items[$filteredName]);
    }

    /**
     * Filter a string by converting properties to their values
     *
     * @param string $string
     * @param array $encountered
     * @return string
     * @throws PropertyNameCycleException
     */
    public function filter($string, $encountered = array())
    {
        while (preg_match('#^(.*)\${(.*?)}(.*)$#', $string, $matches)) {
            if (in_array($matches[2], $encountered)) {
                throw new PropertyNameCycleException();
            }

            $encountered[] = $matches[2];
            $string = $matches[1] . $this->filter($this->{$matches[2]}, $encountered) . $matches[3];
        }
        return $string;
    }

}
