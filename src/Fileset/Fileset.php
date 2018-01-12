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

namespace Pants\Fileset;

use FilesystemIterator;
use Iterator;
use JMS\Serializer\Annotation as JMS;
use Pants\ContextInterface;
use Pants\Fileset\Fileset\MatcherInterface;
use Pants\Fileset\Fileset\WhitelistBlacklistFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Standard fileset
 *
 * @JMS\ExclusionPolicy("all")
 */
class Fileset implements FilesetInterface
{

    /**
     * @JMS\Expose()
     * @JMS\SerializedName("base-directory")
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string
     */
    private $baseDirectory;

    /**
     * Blacklist
     *
     * @JMS\Expose()
     * @JMS\SerializedName("blacklist")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("Pants\Fileset\Fileset\MatcherInterface")
     *
     * @var MatcherInterface|null
     */
    private $blacklist;

    /**
     * Whitelist
     *
     * @JMS\Expose()
     * @JMS\SerializedName("whitelist")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("Pants\Fileset\Fileset\MatcherInterface")
     *
     * @var MatcherInterface|null
     */
    private $whitelist;

    /**
     * Constructor
     *
     * @param string $baseDirectory
     * @param MatcherInterface|null $whitelist
     * @param MatcherInterface|null $blacklist
     */
    public function __construct(
        string $baseDirectory,
        ?MatcherInterface $whitelist = null,
        ?MatcherInterface $blacklist = null
    )
    {
        $this->baseDirectory = $baseDirectory;
        $this->blacklist = $blacklist;
        $this->whitelist = $whitelist;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(ContextInterface $context): Iterator
    {
        $baseDirectory = $context->getProperties()
            ->filter($this->baseDirectory);

        return new WhitelistBlacklistFilterIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $baseDirectory,
                    FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS
                ),
                RecursiveIteratorIterator::CHILD_FIRST
            ),
            $baseDirectory,
            $this->whitelist,
            $this->blacklist
        );
    }
}
