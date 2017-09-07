<?php
/**
 * Pants
 *
 * Copyright (c) 2011, Justin Hendrickson
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

use JMS\Serializer\Annotation as JMS;
use Pants\BuildException;
use Pants\Project;

/**
 * Fileset task
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @package Pants\Task
 */
class Fileset implements Task
{

    /**
     * Base directory
     *
     * @JMS\Expose()
     * @JMS\SerializedName("base-directory")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
     */
    protected $baseDirectory;

    /**
     * Id
     *
     * @JMS\Expose()
     * @JMS\SerializedName("id")
     * @JMS\SkipWhenEmpty()
     * @JMS\Type("string")
     * @JMS\XmlElement(cdata=false)
     *
     * @var string|null
     */
    protected $id;

    /**
     * {@inheritdoc}
     */
    public function execute(Project $project): Task
    {
        if (!$this->getId()) {
            throw new BuildException("No id set");
        }

        //        $fileSet = new FileSetType();
//
//        $fileSet->setBaseDirectory($this->getBaseDirectory());
//
//        // TODO setup the blacklist/whitelist patterns
//
//        $this->getProject()
//            ->getTypes()
//            ->{$this->getId()} = $fileSet;
    }

    /**
     * Get the base directory
     *
     * @return string|null
     */
    public function getBaseDirectory()
    {
        return $this->baseDirectory;
    }

    /**
     * Get the id
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the base directory
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
     * Set the id
     *
     * @param string $id
     * @return self
     */
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }
}
