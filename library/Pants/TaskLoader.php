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

use Zend\Loader\PluginClassLoader;

/**
 *
 */
class TaskLoader extends PluginClassLoader
{

    /**
     * Pre-aliased tasks
     * @var array
     */
    protected $plugins = array(
        "call"             => "Pants\Task\Call",
        "chgrp"            => "Pants\Task\Chgrp",
        "chmod"            => "Pants\Task\Chmod",
        "copy"             => "Pants\Task\Copy",
        "cp"               => "Pants\Task\Copy",
        "delete"           => "Pants\Task\Delete",
        "docblox"          => "Pants\Task\Docblox",
        "exec"             => "Pants\Task\Execute",
        "execute"          => "Pants\Task\Execute",
        "input"            => "Pants\Task\Input",
        "move"             => "Pants\Task\Move",
        "mv"               => "Pants\Task\Move",
        "output"           => "Pants\Task\Output",
        "phpcodesniffer"   => "Pants\Task\PhpCodeSniffer",
        "php_code_sniffer" => "Pants\Task\PhpCodeSniffer",
        "phpscript"        => "Pants\Task\PhpScript",
        "php_script"       => "Pants\Task\PhpScript",
        "property"         => "Pants\Task\Property",
        "propertyfile"     => "Pants\Task\PropertyFile",
        "property_file"    => "Pants\Task\PropertyFile",
        "rm"               => "Pants\Task\Delete",
        "tokenfilter"      => "Pants\Task\TokenFilter",
        "token_filter"     => "Pants\Task\TokenFilter",
        "touch"            => "Pants\Task\Touch"
    );

}
