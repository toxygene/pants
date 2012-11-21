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

namespace Pants\Types;

use InvalidArgumentException;

/**
 * Types container
 *
 * @package Pants\Types
 */
class Types
{

    /**
     * Types
     *
     * @var array
     */
    protected $types = array();

    /**
     * Get a type
     *
     * @param string $name
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function __get($name)
    {
        if (!isset($this->$name)) {
            throw new InvalidArgumentException("There is no type with the name of '{$name}'");
        }

        return $this->types[$name];
    }

    /**
     * Check if a type exists
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->types[$name]);
    }

    /**
     * Set a type
     *
     * @param string $name
     * @param mixed $type
     */
    public function __set($name, $type)
    {
        $this->types[$name] = $type;
    }

    /**
     * Unset a types
     *
     * @param string $name
     * @throws InvalidArgumentException
     */
    public function __unset($name)
    {
        if (!isset($this->$name)) {
            throw new InvalidArgumentException("There is no type with the name of '{$name}'");
        }

        unset($this->types[$name]);
    }

}
