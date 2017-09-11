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

namespace Pants\Fileset;

use FilesystemIterator;
use JMS\Serializer\Annotation as JMS;
use Iterator;
use IteratorAggregate;
use Pants\BuildException;
use Pants\ContextInterface;
use Pants\Fileset\Fileset\MatcherInterface;
use Pants\Fileset\Fileset\Matchers;
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
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
     */
    private $baseDirectory;

    /**
     * Blacklist
     *
     * @JMS\Expose()
     * @JMS\SerializedName("blacklist")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("Pants\Fileset\Fileset\Matchers")
     *
     * @var Matchers|null
     */
    private $blacklist;

    /**
     * Whitelist
     *
     * @JMS\Expose()
     * @JMS\SerializedName("whitelist")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("Pants\Fileset\Fileset\Matchers")
     *
     * @var Matchers|null
     */
    private $whitelist;

    /**
     * {@inheritdoc}
     */
    public function getIterator(ContextInterface $context): Iterator
    {
        if (null === $this->getBaseDirectory()) {
            throw new BuildException();
        }

        $baseDirectory = $context->getProperties()
            ->filter($this->getBaseDirectory());

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $baseDirectory,
                FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        if (null !== $this->getWhitelist() || null !== $this->getBlacklist()) {
            $iterator = new WhitelistBlacklistFilterIterator($iterator);
            $iterator->setBaseDirectory($this->getBaseDirectory());

            if (null !== $this->getBlacklist()) {
                $iterator->setBlacklist($this->getBlacklist());
            }

            if (null !== $this->getWhitelist()) {
                $iterator->setWhitelist($this->getWhitelist());
            }
        }

        return $iterator;
    }

    /**
     * Get the base directory for the fileset
     *
     * @return string|null
     */
    public function getBaseDirectory()
    {
        return $this->baseDirectory;
    }

    /**
     * Get the blacklist
     *
     * @return Matchers|null
     */
    public function getBlacklist()
    {
        return $this->blacklist;
    }

    /**
     * Get the whitelist
     *
     * @return Matchers|null
     */
    public function getWhitelist()
    {
        return $this->whitelist;
    }

    /**
     * Set the base directory for the fileset
     *
     * @param string $baseDirectory
     * @return self
     */
    public function setBaseDirectory(string $baseDirectory): self
    {
        $this->baseDirectory = $baseDirectory;
        return $this;
    }

    /**
     * Set the blacklist
     *
     * @param Matchers $blacklist
     * @return self
     */
    public function setBlacklist(Matchers $blacklist): self
    {
        $this->blacklist = $blacklist;
        return $this;
    }

    /**
     * Set the whitelist
     *
     * @param Matchers $whitelist
     * @return self
     */
    public function setWhitelist(Matchers $whitelist): self
    {
        $this->whitelist = $whitelist;
        return $this;
    }

}
