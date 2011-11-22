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

namespace Pants;

use Pants\BuildException,
    Pants\Project,
    Pants\TaskLoader,
    SimpleXMLElement;

/**
 * Builder
 *
 * @package Pants
 */
class Builder
{

    /**
     * Project being constructed
     * @var Project
     */
    protected $_project;

    /**
     * Task loader for this builder
     * @var TaskLoader
     */
    protected $_taskLoader;

    /**
     * Build a project from an XML file
     *
     * @param string $file
     * @return Project
     */
    public function build($file)
    {
        $sxml = $this->_getSimpleXmlObjectFromFile($file);

        if (isset($sxml['baseDir'])) {
            $this->getProject()
                 ->setBaseDir($sxml['baseDir']);
        }

        foreach ($sxml as $key => $element) {
            switch ($key) {
                // Register task
                case "register_task":
                    $this->getTaskLoader()
                         ->registerPlugin((string) $element['short_name'], (string) $element['class_name']);
                    break;

                // Targets
                case "target":
                    $this->getProject()
                         ->getTargets()
                         ->add($this->_buildTarget($element));
                    break;

                // Tasks
                default:
                    $this->getProject()
                         ->getTasks()
                         ->add($this->_buildTask($key, $element));
                    break;
            }
        }

        return $this->getProject();
    }

    /**
     * Get the project
     *
     * @return Project
     */
    public function getProject()
    {
        if (!$this->_project) {
            $this->_project = new Project();
        }
        return $this->_project;
    }

    /**
     * Get the task loader
     *
     * @return TaskLoader
     */
    public function getTaskLoader()
    {
        if (!$this->_taskLoader) {
            $this->_taskLoader = new TaskLoader();
        }
        return $this->_taskLoader;
    }

    /**
     * Set the project
     *
     * @param Project $project
     * @return Builder
     */
    public function setProject(Project $project)
    {
        $this->_project = $project;
        return $this;
    }

    /**
     * Set the task loader
     *
     * @param TaskLoader $taskLoader
     * @return Builder
     */
    public function setTaskLoader(TaskLoader $taskLoader)
    {
        $this->_taskLoader = $taskLoader;
        return $this;
    }

    /**
     * Build a target from an XML node
     *
     * @param SimpleXMLElement $sxml
     * @return Target
     */
    private function _buildTarget(SimpleXMLElement $sxml)
    {
        $options = array();
        foreach ($sxml->attributes() as $k => $v) {
            $options[(string) $k] = (string) $v;
        }

        $target = new Target($options);

        foreach ($sxml as $key => $element) {
            $target->getTasks()
                   ->add($this->_buildTask($key, $element));
        }

        return $target;
    }

    /**
     * Build a task from an XML node
     *
     * @param string $type
     * @param SimpleXMLElement $sxml
     * @return Task
     */
    private function _buildTask($type, SimpleXMLElement $sxml)
    {
        $options = array();
        foreach ($sxml->attributes() as $k => $v) {
            $options[(string) $k] = (string) $v;
        }

        $className = $this->getTaskLoader()
                          ->load($type);

        if (!$className) {
            throw new BuildException("Unknown type '{$type}'");
        }

        $task = new $className($options);

        foreach ($sxml as $key => $element) {
            if (isset($element["id"])) {
                $task->{"add" . $key}(
                    new LazyLoadedFileSet($this->getProject()->getTypes(), (string) $element["id"])
                );
            } else {
                $type = $task->{"create" . $key}();

                foreach ($element->attributes() as $k => $v) {
                    $type->{"set" . $k}((string) $v);
                }
            }
        }

        return $task;
    }

    /**
     * Create a SimpleXMLElement from a file
     *
     * @param string $file
     * @return SimpleXMLElement
     * @throws BuildException
     */
    private function _getSimpleXmlObjectFromFile($file)
    {
        $useErrors = libxml_use_internal_errors(true);
        $sxml = simplexml_load_file($file);

        if (libxml_get_errors()) {
            $message = implode(
                "; ",
                array_map(
                    function($libXmlError) {
                        return sprintf(
                            "%s (%s on line %s, column %s)",
                            rtrim($libXmlError->message),
                            $libXmlError->file,
                            $libXmlError->line,
                            $libXmlError->column
                        );
                    },
                    libxml_get_errors()
                )
            );

            libxml_use_internal_errors($useErrors);

            throw new BuildException($message);
        }

        libxml_use_internal_errors($useErrors);

        return $sxml;
    }

}
