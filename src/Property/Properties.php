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

namespace Pants\Property;

use InvalidArgumentException;
use JMS\Serializer\Annotation as JMS;

/**
 * Properties container
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Property
 */
class Properties
{

    /**
     * Items
     *
     * @JMS\Expose()
     * @JMS\Inline()
     * @JMS\Type("array<string, string>")
     * @JMS\XmlElement(cdata=false)
     * @JMS\XmlMap(keyAttribute="name", entry="property")
     *
     * @var array
     */
    protected $items = array();

    /**
     * Get a property
     *
     * @param string $name
     * @return string
     * @throws InvalidArgumentException
     */
    public function __get(string $name): string
    {
        $filteredName = $this->filter($name);

        if (!isset($this->$filteredName)) {
            throw new InvalidArgumentException("There is no property with a name of '{$name}'");
        }

        return $this->items[$filteredName];
    }

    /**
     * Check if a property exists
     *
     * @param string $name
     * @return boolean
     */
    public function __isset(string $name): bool
    {
        return isset($this->items[$this->filter($name)]);
    }

    /**
     * Set a property
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value)
    {
        $this->items[$name] = $value;
    }

    /**
     * To string
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function __toString(): string
    {
        $properties = array();
        foreach ($this->items as $key => $value) {
            $properties[] = "{$key} = {$value}";
        }
        return implode("\n", $properties);
    }

    /**
     * Unset a property
     *
     * @param string $name
     */
    public function __unset(string $name)
    {
        $filteredName = $this->filter($name);

        if (!isset($this->$filteredName)) {
            throw new InvalidArgumentException("There is no property with a name of '{$name}'");
        }

        unset($this->items[$filteredName]);
    }

    /**
     * Add multiple properties from an array
     *
     * @param array $properties
     * @return self
     */
    public function add(array $properties): self
    {
        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }

        return $this;
    }

    /**
     * Filter a string by converting properties to their values
     *
     * @param string|int $string
     * @param string[] $encountered
     * @return string|int
     * @throws PropertyNameCycleException
     */
    public function filter($string, array $encountered = array())
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
