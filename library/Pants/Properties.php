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
 *     * The name of its contributor may not be used to endorse or promote
 *       products derived from this software without specific prior written
 *       permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL JUSTIN HENDRICKSON BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
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
