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
 */

namespace Pants;

/**
 *
 */
class Autoloader
{

    /**
     * Class mapping
     * @var array
     */
    private $_classes = array(
        "Pants\Project",
        "Pants\Properties",
        "Pants\Properties\PropertyNameCycleException",
        "Pants\Target",
        "Pants\Targets",
        "Pants\Task",
        "Pants\Task\AbstractFileTask",
        "Pants\Task\AbstractTask",
        "Pants\Task\Call",
        "Pants\Task\Chgrp",
        "Pants\Task\Chmod",
        "Pants\Task\Chown",
        "Pants\Task\Copy",
        "Pants\Task\Delete",
        "Pants\Task\Exception",
        "Pants\Task\Exec",
        "Pants\Task\FileSet",
        "Pants\Task\Move",
        "Pants\Task\Output",
        "Pants\Task\PhpScript",
        "Pants\Task\Property",
        "Pants\Task\Touch",
        "Pants\Tasks"
    );

    /**
     * Path
     * @var string
     */
    private $_path;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_path = dirname(__DIR__) . "/";
    }

    /**
     * Autoloader
     *
     * @param string $className
     */
    public function autoload($className)
    {
        if (in_array($className, $this->getClasses())) {
            require_once $this->getPath() . str_replace("\\", DIRECTORY_SEPARATOR, $className) . ".php";
        }
    }

    /**
     * Get the classes
     *
     * @return array
     */
    public function getClasses()
    {
        return $this->_classes;
    }

    /**
     * Get the path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Register the autoloader
     */
    public function register()
    {
        spl_autoload_register(array($this, "autoload"));
    }

}
