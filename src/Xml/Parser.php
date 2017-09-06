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
 * @package Pants
 * @subpackage Xml
 */

namespace Pants\Xml;

use Pants\Project;
use Pants\Target\Target;
use Pants\Task\Task;
use Pants\Task\TaskLoader;
use Pants\Task\TaskLoaderStack;
use Pants\Xml\RuntimeException;
use XMLReader;

/**
 * XML parser
 *
 * @package Pants
 * @subpackage Xml
 */
class Parser
{

    /**
     * Task loaders
     *
     * @var TaskLoaderStack
     */
    protected $taskLoader;

    /**
     * XML parser
     *
     * @var XMLReader
     */
    protected $xmlReader;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->taskLoader = new TaskLoaderStack();
        $this->taskLoader->add(new TaskLoader());
    }

    /**
     * Parse an XML string
     *
     * @param $xml
     * @return Project
     */
    public function parseXml($xml)
    {
        $this->xmlReader = new XMLReader();
        $this->xmlReader->xml($xml);

        return $this->handleXml();
    }

    /**
     * Parse an XML file
     *
     * @param $file
     * @return Project
     */
    public function parseFile($file)
    {
        $this->xmlReader = new XMLReader();
        $this->xmlReader->open($file);

        return $this->handleXml();
    }

    /**
     * Handle the XML document
     *
     * @return Project
     * @todo register namespaces with autoloaders and task locators
     */
    public function handleXml()
    {
        while ($this->xmlReader->read()) {
            if ($this->xmlReader->name == "project" && $this->xmlReader->nodeType == XMLReader::ELEMENT) {
                // TODO register namespaces with autoloaders and task locators
                return $this->handleProject();
            }
        }
    }

    /**
     * Handle the XML project
     *
     * @return Project
     */
    public function handleProject()
    {
        $project = new Project();
        $project->setDefault($this->xmlReader->getAttribute("default"));

        while ($this->xmlReader->read()) {
            if ($this->xmlReader->name == "project" && $this->xmlReader->nodeType == XMLReader::END_ELEMENT) {
                return $project;
            } elseif ($this->xmlReader->name == "target" && $this->xmlReader->nodeType == XMLReader::ELEMENT) {
                $project->getTargets()
                        ->add($this->handleTarget());
            } elseif ($this->xmlReader->nodeType == XMLReader::ELEMENT) {
                $project->getTasks()
                        ->add($this->handleTask());
            }
        }
    }

    /**
     * Handle an XML task
     *
     * @return Task
     * @throws RuntimeException
     */
    public function handleTask()
    {
        $taskType    = $this->xmlReader->name;
        $isEmptyTask = $this->xmlReader->isEmptyElement;

        $taskClassName = $this->taskLoader->load($taskType);
        if (!$taskClassName) {
            throw new RuntimeException("Could not locate the '{$taskType}' task");
        }

        $task = new $taskClassName();

        if ($this->xmlReader->hasAttributes) {
            $attributeCount = $this->xmlReader->attributeCount;
            for ($i = 0; $i < $attributeCount; ++$i) {
                $this->xmlReader->moveToAttributeNo($i);
                $task->{"set" . $this->xmlReader->name}($this->xmlReader->value);
            }
        }

        if ($isEmptyTask) {
            return $task;
        }

        while ($this->xmlReader->read()) {
            if ($this->xmlReader->name == $taskType && $this->xmlReader->nodeType == XMLReader::END_ELEMENT) {
                return $task;
            }
        }
    }

    /**
     * Handle an XML target
     *
     * @return Target
     */
    public function handleTarget()
    {
        $target = new Target();
        $target->setName($this->xmlReader->getAttribute("name"));

        if ($this->xmlReader->isEmptyElement) {
            return $target;
        }

        while ($this->xmlReader->read()) {
            if ($this->xmlReader->name == "target" && $this->xmlReader->nodeType == XMLReader::END_ELEMENT) {
                return $target;
            } elseif ($this->xmlReader->nodeType == XMLReader::ELEMENT) {
                $target->getTasks()->add($this->handleTask());
            }
        }
    }
}
