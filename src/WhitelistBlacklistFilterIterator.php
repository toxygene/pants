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
 */

declare(strict_types=1);

namespace Pants;

use FilterIterator;
use Iterator;
use Pants\Matcher\MatcherInterface;

/**
 * Whitelist/blacklist pattern filter iterator
 */
class WhitelistBlacklistFilterIterator extends FilterIterator
{
    /**
     * Base directory
     *
     * @var string|null
     */
    protected $baseDirectory;

    /**
     * Blacklist
     *
     * @var MatcherInterface|null
     */
    protected $blacklist;

    /**
     * Whitelist
     *
     * @var MatcherInterface|null
     */
    protected $whitelist;

    /**
     * Constructor
     *
     * @param Iterator $iterator
     * @param string|null $baseDirectory
     * @param MatcherInterface|null $whitelist
     * @param MatcherInterface|null $blacklist
     */
    public function __construct(
        Iterator $iterator,
        ?string $baseDirectory = null,
        ?MatcherInterface $whitelist = null,
        ?MatcherInterface $blacklist = null
    )
    {
        parent::__construct($iterator);

        $this->baseDirectory = $baseDirectory;
        $this->blacklist = $blacklist;
        $this->whitelist = $whitelist;
    }

    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        if (null != $this->whitelist) {
            if (
                !$this->whitelist
                    ->match(
                        $this->getInnerIterator()->current(),
                        $this->baseDirectory
                    )
            ) {
                return false;
            }

            if (
                null != $this->blacklist &&
                $this->blacklist
                    ->match(
                        $this->getInnerIterator()->current(),
                        $this->baseDirectory
                    )
            ) {
                return false;
            }

            return true;
        }

        if (null != $this->blacklist) {
            return !$this->blacklist
                ->match(
                    $this->getInnerIterator()->current(),
                    $this->baseDirectory
                );
        }

        return true;
    }
}
