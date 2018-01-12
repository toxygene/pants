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

namespace Pants\Task;

use ErrorException;
use JMS\Serializer\Annotation as JMS;
use Pants\ContextInterface;
use function Pale\run;

/**
 * Copy file(s) task
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Task
 */
class Copy implements TaskInterface
{

    /**
     * Destination file
     *
     * @JMS\Expose()
     * @JMS\SerializedName("destination")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
     */
    protected $destination;

    /**
     * Source file
     *
     * @var string|null
     */
    protected $source;

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        if (null === $this->getSource()) {
            $message = 'Source not set';

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

        if (null === $this->getDestination()) {
            $message = 'Destination not set';

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

        $source = $context->getProperties()
            ->filter($this->getSource());

        $destination = $context->getProperties()
            ->filter($this->getDestination());

        $context->getLogger()->debug(
            sprintf(
                'Copying source "%s" to destination "%s"',
                $source,
                $destination
            ),
            [
                'target' => $context->getCurrentTarget()
                    ->getName()
            ]
        );

        try {
            run(function () use ($source, $destination) {
                copy($source, $destination);
            });
        } catch (ErrorException $e) {
            $message = sprintf(
                'Could not copy source "%s" to destination "%s" because "%s"',
                $source,
                $destination,
                $e->getMessage()
            );

            $context->getLogger()->error(
                $message,
                [
                    'target' => $context->getCurrentTarget()
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
     * Get the destination
     *
     * @return string|null
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Get the target file
     *
     * @return string|null
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the destination file
     *
     * @param string $destination
     * @return self
     */
    public function setDestination(string $destination): self
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * Set the target file
     *
     * @param string $source
     * @return self
     */
    public function setSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }
}
