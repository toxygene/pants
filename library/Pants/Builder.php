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
     * Build a project from an XML file
     *
     * @param string $file
     * @return Project
     */
    public function build($file)
    {
        $project    = new Project();
        $taskLoader = new TaskLoader();

        $sxml = $this->_getSimpleXmlObjectFromFile($file);

        if (isset($sxml['baseDir'])) {
            $project->setBaseDir($sxml['baseDir']);
        }

        foreach ($sxml as $key => $element) {
            switch ($key) {
                // Register task
                case "register_task":
                    $taskLoader->registerPlugin((string) $element['short_name'], (string) $element['class_name']);
                    break;

                // Targets
                case "target":
                    $options = array();
                    foreach ($element->attributes() as $k => $v) {
                        $options[(string) $k] = (string) $v;
                    }

                    $target = new Target($options);

                    foreach ($element as $subkey => $subElement) {
                        $options = array();
                        foreach ($subElement->attributes() as $k => $v) {
                            $options[(string) $k] = (string) $v;
                        }

                        $className = $taskLoader->load($subkey);
                        $task = new $className($options);

                        $target->getTasks()
                                ->add($task);
                    }

                    $project->getTargets()
                            ->add($target);
                    break;

                // Tasks
                default:
                    $options = array();
                    foreach ($element->attributes() as $k => $v) {
                        $options[(string) $k] = (string) $v;
                    }

                    $className = $taskLoader->load($key);
                    $task = new $className($options);

                    $project->getTasks()
                            ->add($task);
                    break;
            }
        }

        var_dump($taskLoader);

        return $project;
    }

    /**
     *
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
