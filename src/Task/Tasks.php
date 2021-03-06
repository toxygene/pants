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

namespace Pants\Task;

use ArrayIterator;
use Iterator;

/**
 * Tasks container
 *
 * @package Pants\Tasks
 */
class Tasks implements TasksInterface
{
    /**
     * Tasks
     *
     * @var array
     */
    protected $tasks = array();

    /**
     * {@inheritdoc}
     */
    public function add(TaskInterface $task): TasksInterface
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->tasks);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->tasks;
    }
}
