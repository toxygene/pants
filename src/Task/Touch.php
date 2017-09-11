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

namespace Pants\Task;

use ErrorException;
use JMS\Serializer\Annotation as JMS;
use function Pale\run;
use Pants\ContextInterface;

/**
 * Touch file(s) task
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Task
 */
class Touch implements TaskInterface
{

    /**
     * Target path
     *
     * @JMS\Expose()
     * @JMS\SerializedName("path")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
     */
    protected $path;
    
    /**
     * Time to touch the file with
     *
     * @JMS\Expose()
     * @JMS\SerializedName("time")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
     */
    protected $time;

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        if (null === $this->getPath()) {
            $message = 'Path not set';

            $context->getLogger()->error(
                $message,
                [
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            throw new BuildException(
                $message,
                $context->getCurrentTarget(),
                $this
            );
        }

        $path = $context->getProperties()
            ->filter($this->getPath());

        if (null !== $this->getTime()) {
            $time = $context->getProperties()
                ->filter($this->getTime());
        } else {
            $time = time(); // todo this is kind of ugly
        }

        $context->getLogger()->debug(
            sprintf(
                'Touching path "%s" with time "%s"',
                $path,
                $time
            ),
            [
                'target' => $context->getCurrentTarget()
                    ->getName()
            ]
        );

        try {
            run(function() use ($path, $time) {
                touch($path, $time);
            });
        } catch (ErrorException $e) {
            $message = sprintf(
                'Could not touch path "%s" with time "%s"',
                $path,
                $time
            );

            $context->getLogger()->error(
                $message,
                [
                    'target' => $context->getCurrentTarget()
                        ->getName()
                ]
            );

            throw new BuildException(
                $message,
                $context->getCurrentTarget(),
                $this,
                $e
            );
        }

        return $this;
    }

    /**
     * Get the target path
     *
     * @return string|null
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * Get the time to touch the file with
     *
     * @return string|null
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set the target path
     *
     * @param string $path
     * @return self
     */
    public function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }
    
    /**
     * Set the time to touch the file with
     *
     * @param string $time
     * @return self
     */
    public function setTime(string $time): self
    {
        $this->time = $time;
        return $this;
    }
}
