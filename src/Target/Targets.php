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

namespace Pants\Target;

use InvalidArgumentException;

/**
 * Targets container
 *
 * @package Pants\Target
 */
class Targets
{

    /**
     * Targets
     *
     * @var Target[]
     */
    protected $targets = array();

    /**
     * Get a target
     *
     * @param string $name
     * @return Target
     * @throws InvalidArgumentException
     */
    public function __get(string $name): Target
    {
        if (!isset($this->$name)) {
            throw new InvalidArgumentException("There is no target with the name of '{$name}'");
        }

        return $this->targets[$name];
    }

    /**
     * Check if a target exists
     *
     * @param string $name
     * @return boolean
     */
    public function __isset(string $name): bool
    {
        return isset($this->targets[$name]);
    }

    /**
     * Unset a target
     *
     * @param string $name
     * @throws InvalidArgumentException
     */
    public function __unset(string $name)
    {
        if (!isset($this->$name)) {
            throw new InvalidArgumentException("There is no target with the name of '{$name}'");
        }

        unset($this->targets[$name]);
    }

    /**
     * Add a target
     *
     * @param Target $target
     * @return self
     * @throws InvalidArgumentException
     */
    public function add(Target $target): self
    {
        $name = $target->getName();

        if (isset($this->{$name})) {
            throw new InvalidArgumentException("A target already exists with the name of '{$name}'");
        }

        $this->targets[$name] = $target;

        return $this;
    }

    /**
     * Get the names and descriptions of the targets
     *
     * @return string[]
     */
    public function getDescriptions()
    {
        $descriptions = array();
        foreach ($this->targets as $name => $target) {
            if (!$target->getHidden()) {
                $descriptions[$name] = $target->getDescription();
            }
        }
        return $descriptions;
    }

    /**
     * Get all the targets
     *
     * @return Target[]
     */
    public function toArray(): array
    {
        return $this->targets;
    }
}
