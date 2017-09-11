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
 */
class Properties implements PropertiesInterface
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
    protected $properties = array();

    /**
     * {@inheritdoc}
     */
    public function add(string $name, $value): PropertiesInterface
    {
        if (isset($this->properties[$name])) {
            throw new InvalidArgumentException();
        }

        $this->properties[$name] = $value;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name): string
    {
        $filteredName = $this->filter($name);

        if (!isset($this->properties[$filteredName])) {
            throw new InvalidArgumentException("There is no property with a name of '{$name}'");
        }

        return $this->properties[$filteredName];
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $name): bool
    {
        return isset($this->properties[$this->filter($name)]);
    }

    /**
     * {@inheritdoc}
     */
    public function filter($input, array $encountered = array())
    {
        return $this->filterWhileCheckingForCycles($input);
    }

    /**
     * Filter a string while checking for cycles
     *
     * @param int|string $input
     * @param string[] $encountered
     * @return int|string
     */
    protected function filterWhileCheckingForCycles($input, array $encountered = [])
    {
        while (preg_match('#^(.*)\${(.*?)}(.*)$#', $input, $matches)) {
            if (in_array($matches[2], $encountered)) {
                throw new PropertyNameCycleException(); // todo need a message
            }

            $encountered[] = $matches[2];
            $input = $matches[1] . $this->filterWhileCheckingForCycles($this->{$matches[2]}, $encountered) . $matches[3];
        }
        return $input;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $properties): PropertiesInterface
    {
        foreach ($properties as $name => $value) {
            $this->add($name, $value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $name): PropertiesInterface
    {
        $filteredName = $this->filter($name);

        if (!isset($this->properties[$filteredName])) {
            throw new InvalidArgumentException();
        }

        unset($this->properties[$filteredName]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->properties;
    }
}
