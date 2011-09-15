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

use DocBlox_Core_Abstract as CoreAbstract,
    DocBlox_Parser as Parser,
    DocBlox_Parser_Abstract as ParserAbstract,
    DocBlox_Parser_Files as Files,
    DocBlox_Transformer as Transformer,
    Pants\BuildException,
    Pants\FileSet,
    sfEventDispatcher,
    Zend\Loader\StandardAutoloader;

/**
 * Docblox
 *
 * @package Pants
 * @subpackage Task
 */
class Docblox extends AbstractTask
{

    /**
     * FileSet
     * @var FileSet
     */
    protected $_fileSet;

    /**
     * Force documentation
     * @var boolean
     */
    protected $_force;

    /**
     * Library path
     * @var string
     */
    protected $_libraryPath;

    /**
     * Markers
     * @var array
     */
    protected $_markers;

    /**
     * Parse private
     * @var boolean
     */
    protected $_parsePrivate;

    /**
     * Target
     * @var string
     */
    protected $_target;

    /**
     * Templates
     * @var array
     */
    protected $_templates;

    /**
     * Themes path
     * @var string
     */
    protected $_themesPath;

    /**
     * Title
     * @var string
     */
    protected $_title;

    /**
     * Validate
     * @var boolean
     */
    protected $_validate;

    /**
     * Execute the task
     *
     * @return Docblox
     * @throw BuildException
     */
    public function execute()
    {
        if (!$this->getLibraryPath()) {
            throw new BuildException("No Docblox library path set");
        }

        set_include_path(get_include_path() . PATH_SEPARATOR . $this->getLibraryPath());

        require_once 'markdown.php';

        $autoloader = new StandardAutoloader();
        $autoloader->registerPrefix("Zend", $this->getLibraryPath() . "/Zend")
                   ->registerPrefix("DocBlox", $this->getLibraryPath() . "/DocBlox")
                   ->setFallbackAutoloader(true)
                   ->register();

        $parser = new Parser();
        ParserAbstract::$event_dispatcher = new sfEventDispatcher();

        if ($this->getForce()) {
            $parser->setForced($this->getForce());
        }

        if ($this->getMarkers()) {
            $parser->setMarkers($this->getMarkers());
        }

        if ($this->getTitle()) {
            $parser->setTitle($this->getTitle());
        }

        if ($this->getValidate()) {
            $parser->setValidate($this->getValidate());
        }

        $files = new Files();

        foreach ($this->getFileSet() as $file) {
            $files->addFile($file->getPathname());
        }

        $xml = $parser->parseFiles($files);

        $transformer = new Transformer();

        $transformer->setSource($xml);

        if ($this->getParsePrivate()) {
            $transformer->setParseprivate($this->getParsePrivate());
        }

        if ($this->getTarget()) {
            $transformer->setTarget($this->getTarget());
        }

        if ($this->getThemesPath()) {
            $transformer->setThemesPath($this->getThemesPath());
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
     * Get the file set
     *
     * @return FileSet
     */
    public function getFileSet()
    {
        return $this->_fileSet;
    }

    /**
     * Get the force full documentation flag
     *
     * @return boolean
     */
    public function getForce()
    {
        return $this->_force;
    }

    /**
     * Get the path to the DocBlox library files
     *
     * @return string
     */
    public function getLibraryPath()
    {
        return $this->_libraryPath;
    }

    /**
     * Get the markers
     *
     * @return array
     */
    public function getMarkers()
    {
        return $this->_markers;
    }

    /**
     * Get the parse private flag
     *
     * @return string
     */
    public function getParsePrivate()
    {
        return $this->_parsePrivate;
    }

    /**
     * Get the target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->_target;
    }

    /**
     * Get the templates
     *
     * @return string
     */
    public function getTemplates()
    {
        return $this->_templates;
    }

    /**
     * Get the themes path
     *
     * @return string
     */
    public function getThemesPath()
    {
        return $this->_themesPath;
    }

    /**
     * Get the title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Get the validate flag
     *
     * @return boolean
     */
    public function getValidate()
    {
        return $this->_validate;
    }

    /**
     * Set the file set
     *
     * @param FileSet $fileSet
     * @return Docblox
     */
    public function setFileSet(FileSet $fileSet)
    {
        $this->_fileSet = $fileSet;
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
        $this->_force = $force;
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
        $this->_libraryPath = $libraryPath;
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
        $this->_markers = $Markers;
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
        $this->_parsePrivate = $parsePrivate;
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
        $this->_target = $target;
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
        $this->_templates = $templates;
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
        $this->_themesPath = $themesPath;
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
        $this->_title = $title;
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
        $this->_validate = $validate;
        return $this;
    }

}
