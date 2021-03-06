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

namespace Pants\Jms;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\VisitorInterface;
use Pants\Matcher\CompositeMatcher;
use Pants\Matcher\MatcherInterface;
use Pants\Property\Properties;
use Pants\Target\Target;
use Pants\Target\Targets;
use Pants\Task\TaskInterface;
use Pants\Task\Tasks;

/**
 * JMS subscribing handler for Targets, Tasks, Matchers, and Properties collections.
 */
class CollectionsHandler implements SubscribingHandlerInterface
{

    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods(): array
    {
        $methods = [];
        $formats = ['json', 'xml', 'yml'];

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

            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => CompositeMatcher::class,
                'format' => $format,
                'method' => 'serializeMatchers'
            ];

            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'type' => CompositeMatcher::class,
                'format' => $format,
                'method' => 'deserializeMatchers'
            ];

            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => Properties::class,
                'format' => $format,
                'method' => 'serializeProperties'
            ];

            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'type' => Properties::class,
                'format' => $format,
                'method' => 'deserializeProperties'
            ];
        }

        return $methods;
    }

    /**
     * Serialize a Pants\Target\Targets object
     *
     * @param VisitorInterface $visitor
     * @param Targets $targets
     * @param array $type
     * @param Context $context
     * @return array
     */
    public function serializeTargets(
        VisitorInterface $visitor,
        Targets $targets,
        array $type,
        Context $context
    ): array {
        $type['name'] = 'array';
        $type['params'] = [
            [
                'name' => Target::class
            ]
        ];

        return $visitor->visitArray($targets->toArray(), $type, $context);
    }

    /**
     * Deserialize a Pants\Target\Targets object
     *
     * @param VisitorInterface $visitor
     * @param array $data
     * @param array $type
     * @param Context $context
     * @return Targets
     */
    public function deserializeTargets(
        VisitorInterface $visitor,
        $data,
        array $type,
        Context $context
    ) {
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

    /**
     * Serialize a Pants\Task\Tasks object
     *
     * @param VisitorInterface $visitor
     * @param Tasks $tasks
     * @param array $type
     * @param Context $context
     * @return array
     */
    public function serializeTasks(
        VisitorInterface $visitor,
        Tasks $tasks,
        array $type,
        Context $context
    ): array {
        $type['name'] = 'array';
        $type['params'] = [
            [
                'name' => TaskInterface::class
            ]
        ];

        return $visitor->visitArray($tasks->toArray(), $type, $context);
    }

    /**
     * Deserialize a Pants\Task\Tasks object
     *
     * @param VisitorInterface $visitor
     * @param array $data
     * @param array $type
     * @param Context $context
     * @return Tasks
     */
    public function deserializeTasks(
        VisitorInterface $visitor,
        $data,
        array $type,
        Context $context
    ): Tasks {
        $tasks = new Tasks();

        $type['name'] = 'array';
        $type['params'] = [
            [
                'name' => TaskInterface::class
            ]
        ];

        foreach ($visitor->visitArray($data, $type, $context) as $task) {
            $tasks->add($task);
        }

        return $tasks;
    }

    /**
     * Serialize a Pants\Fileset\Fileset\Matchers object
     *
     * @param VisitorInterface $visitor
     * @param CompositeMatcher $matchers
     * @param array $type
     * @param Context $context
     * @return array
     */
    public function serializeMatchers(
        VisitorInterface $visitor,
        CompositeMatcher $matchers,
        array $type,
        Context $context
    ): array {
        $type['name'] = 'array';
        $type['params'] = [
            'name' => MatcherInterface::class
        ];

        return $visitor->visitArray($matchers->getMatchers(), $type, $context);
    }

    /**
     * Deserialize a Pants\Fileset\Fileset\Matchers object
     *
     * @param VisitorInterface $visitor
     * @param array $data
     * @param array $type
     * @param Context $context
     * @return CompositeMatcher
     */
    public function deserializeMatchers(
        VisitorInterface $visitor,
        $data,
        array $type,
        Context $context
    ): CompositeMatcher {
        $type['name'] = 'array';
        $type['params'] = [
            [
                'name' => MatcherInterface::class
            ]
        ];

        foreach ($visitor->visitArray($data, $type, $context) as $matcher) {
            $matchers[] = $matcher;
        }

        return new CompositeMatcher($matchers);
    }

    /**
     * Serialize a Pants\Properties object
     *
     * @param VisitorInterface $visitor
     * @param Properties $properties
     * @param array $type
     * @param Context $context
     * @return array
     */
    public function serializeProperties(
        VisitorInterface $visitor,
        Properties $properties,
        array $type,
        Context $context
    ): array {
        $type['name'] = 'array';
        $type['params'] = [
            'name' => 'string'
        ];

        return $visitor->visitArray($properties->toArray(), $type, $context);
    }

    /**
     * Deserialize a Pants\Properties object
     *
     * @param VisitorInterface $visitor
     * @param array $data
     * @param array $type
     * @param Context $context
     * @return Properties
     */
    public function deserializeProperties(
        VisitorInterface $visitor,
        $data,
        array $type,
        Context $context
    ): Properties {
        $properties = new Properties();

        foreach ($data as $key => $value) {
            $properties->add($key, $value);
        }

        return $properties;
    }
}
