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

namespace Pants;

use Pants\Project;
use Pants\Property\Properties;
use Pants\Target\Targets;
use Pants\Task\Container as TaskContainer;
use Pants\Task\Tasks;
use Pimple;

/**
 * Dependency injection container
 *
 * @package Pants
 */
class Container extends Pimple
{

    /**
     * {@inheritDoc}
     */
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $this['project'] = function() {
            return new Project(
                $this['properties'],
                $this['targets'],
                $this['tasks']
            );
        };

        $this['properties'] = function() {
            return new Properties();
        };

        $this['targets'] = function() {
            return new Targets();
        };
        
        $this['task'] = new TaskContainer(array(
            'properties' => $this['properties'],
            'targets'    => $this['targets']
        ));
        
        $this['tasks'] = $this->factory(function() {
            return new Tasks();
        });
    }

}
