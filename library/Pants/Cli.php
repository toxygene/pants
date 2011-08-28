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

use Pile\Autoloader as PileAutoloader,
    Pants\Project,
    Zend_Console_Getopt as Getopt,
    Zend_Console_Exception as ConsoleException,
    Zend_Loader_Autoloader as ZendAutoloader;

/**
 *
 */
class Cli
{

    /**
     * Run the cli
     *
     * @param array $argv
     */
    public function run()
    {
        require_once "Zend/Loader/Autoloader.php";
        ZendAutoloader::getInstance();

        require_once "Pile/Autoloader.php";
        $pileAutoloader = new PileAutoloader();
        $pileAutoloader->register();

        try {
            $opts = new Getopt(
                array(
                    "file|f=s"  => "Set the build file (defaults to build.php)",
                    "help|h"    => "Print help message",
                    "list|l"    => "Print a list of targets",
                    "verbose|v" => "Make temp more verbose"
                )
            );

            $opts->parse();
        } catch (ConsoleException $e) {
            echo $opts->getUsageMessage();
            die(255);
        }

        if ($opts->getOption("h")) {
            echo $opts->getUsageMessage();
            die;
        }

        $project = new Project();

        $file = $opts->getOption("f");
        if (!$file) {
            $file = "build.php";
        }

        require_once $file;

        $project->execute($opts->getRemainingArgs());
    }

}