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

namespace Pants\Target\Executor;

use Pants\ContextInterface;
use Pants\Target\TargetCycleException;
use Pants\Target\TargetInterface;
use Pants\Target\TargetsInterface;

/**
 * Standard executor
 */
class Executor implements ExecutorInterface
{
    /**
     * @var TargetsInterface
     */
    private $targets;

    /**
     * @var TargetInterface[]
     */
    private $targetsStack = [];

    /**
     * @var TargetInterface[]
     */
    private $executedTargets = [];

    /**
     * Constructor
     *
     * @param TargetsInterface $targets
     */
    public function __construct(TargetsInterface $targets)
    {
        $this->targets = $targets;
    }

    /**
     * Get the current target
     *
     * @return TargetInterface|null
     */
    public function getCurrentTarget()
    {
        return end($this->targetsStack);
    }

    /**
     * Execute multiple targets
     *
     * @param string[] $targetNames
     * @param ContextInterface $context
     * @return ExecutorInterface
     */
    public function executeMultiple(array $targetNames, ContextInterface $context): ExecutorInterface
    {
        foreach ($targetNames as $targetName) {
            $this->executeSingle($targetName, $context);
        }

        return $this;
    }

    /**
     * Execute a single target
     *
     * @param string $targetName
     * @param ContextInterface $context
     * @return ExecutorInterface
     */
    public function executeSingle(string $targetName, ContextInterface $context): ExecutorInterface
    {
        $target = $this->targets
            ->get($targetName);

        if (in_array($target, $this->targetsStack)) {
            throw new TargetCycleException($target, $this->targetsStack);
        }

        if (in_array($target, $this->executedTargets)) {
            return $this;
        }

        $this->targetsStack[] = $target;

        $target->execute($context);

        $this->executedTargets[] = array_pop($this->targetsStack);

        return $this;
    }
}
