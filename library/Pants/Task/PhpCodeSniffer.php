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

use Pale\Pale;
use Pants\BuildException;
use Pants\FileSet\FileSet;
use PHP_CodeSniffer as CodeSniffer;
use PHP_CodeSniffer_Reporting as Reporting;

/**
 * PHP CodeSniffer
 *
 * @package Pants\Task
 * @todo Support multiple formatters
 * @todo Filter properties
 */
class PhpCodeSniffer extends AbstractTask
{

    /**
     * FileSet
     *
     * @var FileSet
     */
    protected $fileSet;

    /**
     * Report file
     *
     * @var string
     */
    protected $reportFile;

    /**
     * Report type
     *
     * @var string
     */
    protected $reportType = "summary";

    /**
     * Report width
     *
     * @var integer
     */
    protected $reportWidth = 80;

    /**
     * Show sources flag
     *
     * @var boolean
     */
    protected $showSources = false;

    /**
     * Show warnings flag
     *
     * @var boolean
     */
    protected $showWarnings = false;

    /**
     * Standard
     *
     * @var string
     */
    protected $standard;

    /**
     * Execute the task
     *
     * @return Docblox
     * @throw BuildException
     */
    public function execute()
    {
        if (!$this->getStandard()) {
            throw new BuildException("No standard set");
        }

        if (!class_exists("CodeSniffer")) {
            Pale::run(function() {
                require_once "PHP/CodeSniffer.php";
            });
        }

        if (CodeSniffer::isInstalledStandard($this->getStandard()) === false) {
            throw new BuildException("Invalid standard name");
        }

        $files = array();
        foreach ($this->getFileSet() as $file) {
            $files[] = $file->getRealPath();
        }

        // Clear out argv so PHP_CodeSniffer doesn"t freak out
        $oldArgv = $SERVER["argv"];
        $SERVER["argv"] = array();
        $SERVER["argc"] = 0;

        // Get the current working directory because PHP_CodeSniffer will change it
        $cwd = getcwd();

        $codeSniffer = new CodeSniffer(0, 0, "UTF-8");

        $codeSniffer->process($files, $this->filterProperties($this->getStandard()));

        // Restore the argv/c superglobals
        $SERVER["argv"] = $oldArgv;
        $SERVER["argc"] = count($oldArgv);

        // Reset the current working directory
        chdir($cwd);

        $filesViolations = $codeSniffer->getFilesErrors();
        $reporting       = new Reporting();
        $report          = $reporting->prepare($filesViolations, $this->getShowWarnings());

        $reporting->printReport(
            $this->getReportType(),
            $filesViolations,
            $this->getShowSources(),
            $this->getReportFile(),
            $this->getReportWidth()
        );

        return $this;
    }

    /**
     * Get the file set
     *
     * @return FileSet
     */
    public function getFileSet()
    {
        return $this->fileSet;
    }

    /**
     * Get the report file
     *
     * @return string
     */
    public function getReportFile()
    {
        return $this->reportFile;
    }

    /**
     * Get the report type
     *
     * @return string
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    /**
     * Get the report width
     *
     * @return string
     */
    public function getReportWidth()
    {
        return $this->reportWidth;
    }

    /**
     * Get the show sources flag
     *
     * @return boolean
     */
    public function getShowSources()
    {
        return $this->showSources;
    }

    /**
     * Get the show warnings flag
     *
     * @return boolean
     */
    public function getShowWarnings()
    {
        return $this->showWarnings;
    }

    /**
     * Get the standard
     *
     * @return string
     */
    public function getStandard()
    {
        return $this->standard;
    }

    /**
     * Set the file set
     *
     * @param FileSet $fileSet
     * @return PhpCodeSniffer
     */
    public function setFileSet(FileSet $fileSet)
    {
        $this->fileSet = $fileSet;
        return $this;
    }

    /**
     * Set the report file
     *
     * @param string $reportFile
     * @return PhpCodeSniffer
     */
    public function setReportFile($reportFile)
    {
        $this->reportFile = $reportFile;
        return $this;
    }

    /**
     * Set the report type
     *
     * @param string $reportType
     * @return PhpCodeSniffer
     */
    public function setReportType($reportType)
    {
        $this->reportType = $reportType;
        return $this;
    }

    /**
     * Set the report width
     *
     * @param integer $reportWidth
     * @return PhpCodeSniffer
     */
    public function setReportWidth($reportWidth)
    {
        $this->reportWidth = $reportWidth;
        return $this;
    }

    /**
     * Set the show sources flag
     *
     * @param boolean $showSources
     * @return PhpCodeSniffer
     */
    public function setShowSources($showSources)
    {
        $this->showSources = $showSources;
        return $this;
    }

    /**
     * Set the show warnings flag
     *
     * @param boolean $showWarnings
     * @return PhpCodeSniffer
     */
    public function setShowWarnings($showWarnings)
    {
        $this->showWarnings = $showWarnings;
        return $this;
    }

    /**
     * Set the standard
     *
     * @param string $standard
     * @return PhpCodeSniffer
     */
    public function setStandard($standard)
    {
        $this->standard = $standard;
        return $this;
    }

}
