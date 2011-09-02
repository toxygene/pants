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
 */

namespace Pants\Task;

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
        $name  = $this->filterProperties($this->getName());
        $value = $this->filterProperties($this->getValue());

        $properties = $this->getProject()
                           ->getProperties();

        $this->getProject()->getProperties()->{$name} = $value;

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
