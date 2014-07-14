<?php
/**
 * Pants
 *
 * Copyright (c) 2014, Justin Hendrickson
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

namespace Pants\Task;

use DocBlox_Core_Abstract as CoreAbstract;
use DocBlox_Parser as Parser;
use DocBlox_Parser_Abstract as ParserAbstract;
use DocBlox_Parser_Files as Files;
use DocBlox_Transformer as Transformer;
use Pants\BuildException;
use Pants\Property\Properties;
use sfEventDispatcher;
use Zend\Loader\StandardAutoloader;

/**
 * Docblox
 *
 * @package Pants\Task
 * @TODO figure out what needs to be filtered
 */
class Docblox implements Task
{

    /**
     * Files
     *
     * @var string
     */
    protected $files = array();

    /**
     * Force documentation
     *
     * @var boolean
     */
    protected $force;

    /**
     * Library path
     *
     * @var string
     */
    protected $libraryPath;

    /**
     * Markers
     *
     * @var array
     */
    protected $markers;

    /**
     * Parse private
     *
     * @var boolean
     */
    protected $parsePrivate;

    /**
     * Properties
     *
     * @var Propreties
     */
    protected $properties;

    /**
     * Target
     *
     * @var string
     */
    protected $target;

    /**
     * Templates
     *
     * @var array
     */
    protected $templates;

    /**
     * Themes path
     *
     * @var string
     */
    protected $themesPath;

    /**
     * Title
     *
     * @var string
     */
    protected $title;

    /**
     * Validate
     *
     * @var boolean
     */
    protected $validate;
    
    /**
     * Constructor
     *
     * @param Properties $properties
     */
    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    /**
     * Execute the task
     *
     * @return Docblox
     * @throw BuildException
     */
    public function execute()
    {
        if (!class_exists('Parser')) {
            if (!$this->getLibraryPath()) {
                throw new BuildException('No Docblox library path set');
            }

            $libraryPath = $this->filterProperties($this->getLibraryPath());

            set_include_path(get_include_path() . PATH_SEPARATOR . $libraryPath);

            require_once 'markdown.php';

            $autoloader = new StandardAutoloader();
            $autoloader->registerPrefix('Zend', "{$libraryPath}/Zend")
                       ->registerPrefix('DocBlox',"{$libraryPath}/DocBlox")
                       ->setFallbackAutoloader(true)
                       ->register();
        }

        $parser = new Parser();
        ParserAbstract::$event_dispatcher = new sfEventDispatcher();

        if ($this->getForce()) {
            $parser->setForced($this->getForce());
        }

        if ($this->getMarkers()) {
            $parser->setMarkers($this->getMarkers());
        }

        if ($this->getTitle()) {
            $parser->setTitle($this->filterProperties($this->getTitle()));
        }

        if ($this->getValidate()) {
            $parser->setValidate($this->getValidate());
        }

        $files = new Files();

        foreach ($this->getFiles() as $file) {
            $files->addFile($this->filterProperties($file));
        }

        $xml = $parser->parseFiles($files);

        $transformer = new Transformer();

        $transformer->setSource($xml);

        if ($this->getParsePrivate()) {
            $transformer->setParseprivate($this->getParsePrivate());
        }

        if ($this->getTarget()) {
            $transformer->setTarget($this->filterProperties($this->getTarget()));
        }

        if ($this->getThemesPath()) {
            $transformer->setThemesPath($this->filterProperties($this->getThemesPath()));
        } else {
            $transformer->setThemesPath(CoreAbstract::config()->paths->themes);
        }

        if ($this->getTemplates()) {
            $transformer->setTemplates($this->getTemplates());
        } else {
            $transformer->setTemplates(CoreAbstract::config()->transformations->template->name);
        }

        $transformer->execute();

        return $this;
    }

    /**
     * Get the files
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Get the force full documentation flag
     *
     * @return boolean
     */
    public function getForce()
    {
        return $this->force;
    }

    /**
     * Get the path to the DocBlox library files
     *
     * @return string
     */
    public function getLibraryPath()
    {
        return $this->libraryPath;
    }

    /**
     * Get the markers
     *
     * @return array
     */
    public function getMarkers()
    {
        return $this->markers;
    }

    /**
     * Get the parse private flag
     *
     * @return string
     */
    public function getParsePrivate()
    {
        return $this->parsePrivate;
    }

    /**
     * Get the properties
     *
     * @return Properties
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Get the target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Get the templates
     *
     * @return string
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * Get the themes path
     *
     * @return string
     */
    public function getThemesPath()
    {
        return $this->themesPath;
    }

    /**
     * Get the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the validate flag
     *
     * @return boolean
     */
    public function getValidate()
    {
        return $this->validate;
    }

    /**
     * Set the files
     *
     * @param array $files
     * @return Docblox
     */
    public function setFiles($files)
    {
        $this->files = $files;
        return $this;
    }

    /**
     * Set the force full documentation flag
     *
     * @param boolean $force
     * @return Docblox
     */
    public function setForce($force)
    {
        $this->force = $force;
        return $this;
    }

    /**
     * Set the DocBlox library path
     *
     * @param string $libraryPath
     * @return Docblox
     */
    public function setLibraryPath($libraryPath)
    {
        $this->libraryPath = $libraryPath;
        return $this;
    }

    /**
     * Set the markers
     *
     * @param array $markers
     * @return Docblox
     */
    public function setMarkers(array $markers)
    {
        $this->markers = $Markers;
        return $this;
    }

    /**
     * Set the parse private flag
     *
     * @param boolean $parsePrivate
     * @return Docblox
     */
    public function setParsePrivate($parsePrivate)
    {
        $this->parsePrivate = $parsePrivate;
        return $this;
    }

    /**
     * Set the target
     *
     * @param string $target
     * @return Docblox
     */
    public function setTarget($target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Set the templates
     *
     * @param string $templates
     * @return Docblox
     */
    public function setTemplates($templates)
    {
        $this->templates = $templates;
        return $this;
    }

    /**
     * Set the themes path
     *
     * @param string $themesPath
     * @return Docblox
     */
    public function setThemesPath($themesPath)
    {
        $this->themesPath = $themesPath;
        return $this;
    }

    /**
     * Set the title
     *
     * @param string $title
     * @return Docblox
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set the validate flag
     *
     * @param boolean $validate
     * @return Docblox
     */
    public function setValidate($validate)
    {
        $this->validate = $validate;
        return $this;
    }

}
