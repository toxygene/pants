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

use ErrorException;
use JMS\Serializer\Annotation as JMS;
use Pants\ContextInterface;
use function Pale\run;
use Pants\Task\Exception\TaskException;
use Pants\Task\Exception\MissingPropertyException;

/**
 * Change the current working directory task
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Task
 */
class Chdir implements TaskInterface
{

    /**
     * Target directory
     *
     * @JMS\Expose()
     * @JMS\SerializedName("directory")
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string
     */
    protected $directory;

    /**
     * Constructor
     *
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ContextInterface $context): TaskInterface
    {
        if (empty($this->directory)) {
            throw new MissingPropertyException(
                'directory',
                $context->getCurrentTarget(),
                $this
            );
        }

        $directory = $context->getProperties()
            ->filter($this->directory);

        $context->getLogger()->debug(
            'Changing current working directory to "{directory}"',
            [
                'directory' => $directory,
                'current_target' => $context->getCurrentTarget()
                    ->getName()
            ]
        );

        try {
            run(function () use ($directory) {
                return chdir($directory);
            });
        } catch (ErrorException $e) {
            throw new TaskException(
                sprintf(
                    'Could not change the current working directory to "%s" because "%s"',
                    $directory,
                    $e->getMessage()
                ),
                $context->getCurrentTarget(),
                $this,
                $e
            );
        }

        return $this;
    }
}
