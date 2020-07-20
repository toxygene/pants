<?php
/**
 * Pants
 *
 * Copyright (c) 2011-2018, Justin Hendrickson
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

declare(strict_types=1);

namespace Pants\Property;

/**
 * Properties interface
 */
interface PropertiesInterface
{
    /**
     * Name of the default target property
     *
     * @var string
     */
    const DEFAULT_TARGET_NAME = 'project.default-target';

    /**
     * Add a property
     *
     * @param string $key
     * @param string|int $value
     * @return PropertiesInterface
     */
    public function add(string $key, $value): self;

    /**
     * Check if a property exists
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool;

    /**
     * Filter a string
     *
     * @param string|int $input
     * @return string|int
     * @throws PropertyNameCycleException
     */
    public function filter($input);

    /**
     * Get a property
     *
     * @param string $key
     * @return string|int|false
     */
    public function get(string $key);

    /**
     * Merge an array of properties
     *
     * @param array $properties
     * @return PropertiesInterface
     */
    public function merge(array $properties): self;

    /**
     * Remove a property
     *
     * @param string $key
     * @return PropertiesInterface
     */
    public function remove(string $key): self;

    /**
     * Get the properties
     *
     * @return array
     */
    public function toArray(): array;
}
