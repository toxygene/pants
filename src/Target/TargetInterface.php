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
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 'AS IS'
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

namespace Pants\Target;

use Pants\ContextInterface;
use Pants\Task\TaskInterface;
use Pants\Task\TasksInterface;

/**
 * Interface for a target
 */
interface TargetInterface
{
    /**
     * Get the depends
     *
     * @return string[]
     */
    public function getDepends(): array;

    /**
     * Get the description
     *
     * @return string|null
     */
    public function getDescription();

    /**
     * Get the hidden flag
     *
     * @return boolean
     */
    public function getHidden(): bool;

    /**
     * Get the if conditionals
     *
     * @return string[]
     */
    public function getIf(): array;

    /**
     * Get the name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the tasks
     *
     * @return TasksInterface|TaskInterface[]
     */
    public function getTasks(): TasksInterface;

    /**
     * Get the unless conditionals
     *
     * @return string[]
     */
    public function getUnless(): array;

    /**
     * Execute the task
     *
     * @param ContextInterface $context
     * @return self
     */
    public function execute(ContextInterface $context): self;
}
