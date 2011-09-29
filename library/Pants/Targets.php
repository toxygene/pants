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

namespace Pants;

use InvalidArgumentException,
    Pants\Target;

/**
 * Targets container
 *
 * @package Pants
 */
class Targets
{

    /**
     * Targets
     * @var array
     */
    protected $_targets = array();

    /**
     * Get a target
     *
     * @param string $name
     * @return Target
     * @throws InvalidArgumentException
     */
    public function __get($name)
    {
        if (!isset($this->$name)) {
            throw new InvalidArgumentException("There is no target with the name of '{$name}'");
        }

        return $this->_targets[$name];
    }

    /**
     * Check if a target exists
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->_targets[$name]);
    }

    /**
     * Unset a target
     *
     * @param string $name
     * @throws InvalidArgumentException
     */
    public function __unset($name)
    {
        if (!isset($this->$name)) {
            throw new InvalidArgumentException("There is no target with the name of '{$name}'");
        }

        unset($this->_targets[$name]);
    }

    /**
     * Add a target
     *
     * @param Target $target
     * @return Targets
     * @throws InvalidArgumentException
     */
    public function add(Target $target)
    {
        $name = $target->getName();

        if (isset($this->{$name})) {
            throw new InvalidArgumentException("A target already exists with the name of '{$name}'");
        }

        $this->_targets[$name] = $target;

        return $this;
    }

    /**
     * Get the names and descriptions of the targets
     *
     * @return array
     */
    public function getDescriptions()
    {
        $descriptions = array();
        foreach ($this->_targets as $key => $value) {
            if (!$value->getHidden()) {
                $descriptions[$key] = $value->getDescription();
            }
        }
        return $descriptions;
    }

}
