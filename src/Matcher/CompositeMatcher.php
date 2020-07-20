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

namespace Pants\Matcher;

use ArrayIterator;
use Iterator;
use IteratorAggregate;
use JMS\Serializer\Annotation as JMS;
use Pants\Matcher\MatcherInterface;
use SplFileInfo;

/**
 * Composite matcher
 *
 * @JMS\ExclusionPolicy("all")
 */
class CompositeMatcher implements IteratorAggregate, MatcherInterface
{
    /**
     * @JMS\Expose()
     * @JMS\SerializedName("matchers")
     * @JMS\Type("array<Pants\Fileset\Fileset\MatcherInterface>")
     *
     * @var MatcherInterface[]
     */
    private $matchers = [];

    /**
     * Constructor
     *
     * @param MatcherInterface[] $matchers
     */
    public function __construct(array $matchers)
    {
        foreach ($matchers as $matcher) {
            $this->addMatcher($matcher);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->matchers);
    }

    /**
     * Get the matchers
     *
     * @return MatcherInterface[]
     */
    public function getMatchers(): array
    {
        return $this->matchers;
    }

    /**
     * {@inheritdoc}
     */
    public function match(SplFileInfo $file, string $baseDirectory = null): bool
    {
        foreach ($this->matchers as $matcher) {
            if ($matcher->match($file, $baseDirectory)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a matcher
     *
     * @param MatcherInterface $matcher
     * @return self
     */
    private function addMatcher(MatcherInterface $matcher): self
    {
        $this->matchers[] = $matcher;
        return $this;
    }

}
