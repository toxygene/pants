<?php
/**
 * Pants
 *
 * Copyright (c) 2017, Justin Hendrickson
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

namespace Pants\Jms;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\VisitorInterface;
use Pants\Target\Target;
use Pants\Target\Targets;
use Pants\Task\AbstractTask;
use Pants\Task\Tasks;

class CollectionsHandler implements SubscribingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods(): array
    {
        $methods = array();
        $formats = array('json', 'xml', 'yml');

        foreach ($formats as $format) {
            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => Targets::class,
                'format' => $format,
                'method' => 'serializeTargets'
            ];

            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'type' => Targets::class,
                'format' => $format,
                'method' => 'deserializeTargets'
            ];

            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => Tasks::class,
                'format' => $format,
                'method' => 'serializeTasks'
            ];

            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'type' => Tasks::class,
                'format' => $format,
                'method' => 'deserializeTasks'
            ];
        }

        return $methods;
    }

    public function serializeTargets(VisitorInterface $visitor, Targets $targets, array $type, Context $context)
    {
        $type['name'] = 'array';
        $type['params'] = [
            [
                'name' => Target::class
            ]
        ];

        return $visitor->visitArray($targets->toArray(), $type, $context);
    }

    public function deserializeTargets(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        $targets = new Targets();

        $type['name'] = 'array';
        $type['params'] = [
            [
                'name' => Target::class
            ]
        ];

        foreach ($visitor->visitArray($data, $type, $context) as $target) {
            $targets->add($target);
        }

        return $targets;
    }

    public function serializeTasks(VisitorInterface $visitor, Tasks $tasks, array $type, Context $context)
    {
        $type['name'] = 'array';
        $type['params'] = [
            [
                'name' => AbstractTask::class
            ]
        ];

        return $visitor->visitArray($tasks->toArray(), $type, $context);
    }

    public function deserializeTasks(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        $tasks = new Tasks();

        $type['name'] = 'array';
        $type['params'] = [
            [
                'name' => AbstractTask::class
            ]
        ];

        foreach ($visitor->visitArray($data, $type, $context) as $task) {
            $tasks->add($task);
        }

        return $tasks;
    }
}
