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

use JMS\Serializer\Annotation as JMS;
use Pants\ContextInterface;
use Pants\Task\Exception\MissingPropertyException;
use Pants\Task\Exception\TaskException;

/**
 * Output task
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Task
 */
class Output implements TaskInterface
{
    /**
     * Flag to append a newline
     *
     * @JMS\Expose()
     * @JMS\SerializedName("append-newline")
     * @JMS\Type("boolean")
     * @JMS\XmlElement(cdata=false)
     *
     * @var boolean
     */
    protected $appendNewline;

    /**
     * Message to display
     *
     * @JMS\Expose()
     * @JMS\SerializedName("message")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string
     */
    protected $message;

    /**
     * Constructor
     *
     * @param string $message
     * @param boolean $appendNewline
     */
    public function __construct(
        string $message,
        boolean $appendNewline = true
    )
    {
        $this->appendNewline = $appendNewline;
        $this->message = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        $message = $context->getProperties()
            ->filter($this->message);

        if (empty($message)) {
            throw new MissingPropertyException(
                'message',
                $context->getCurrentTarget(),
                $this
            );
        }

        $context->getLogger()->debug(
            'Outputting message "{message}"',
            [
                'message' => $message,
                'target' => $context->getCurrentTarget()
            ]
        );

        echo $message;

        if ($this->appendNewline ?? true) {
            echo PHP_EOL;
        }

        return $this;
    }
}
