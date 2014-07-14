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

use Pimple;

/**
 * Task dependency injection container
 *
 * @package Pants\Task
 */
class Container extends Pimple
{

    /**
     * {@inheritDoc}
     */
    public function __construct(array $values = array())
    {
        parent::__construct($values);
        
        $this['call'] = $this->factory(function($container) {
            return new Call(
                $container['properties'],
                $container['targets']
            );
        });
        
        $this['chdir'] = $this->factory(function($container) {
            return new Chdir(
                $container['properties']
            );
        });
        
        $this['chgrp'] = $this->factory(function($container) {
            return new Chgrp(
                $container['properties']
            );
        });
        
        $this['chmod'] = $this->factory(function($container) {
            return new Chmod(
                $container['properties']
            );
        });
        
        $this['chown'] = $this->factory(function($container) {
            return new Chown(
                $container['properties']
            );
        });
        
        $this['copy'] = $this->factory(function($container) {
            return new Copy(
                $container['properties']
            );
        });
        
        $this['delete'] = $this->factory(function($container) {
            return new Delete(
                $container['properties']
            );
        });
        
        $this['docblox'] = $this->factory(function($container) {
            return new Docblox(
                $container['properties']
            );
        });

        $this['execute'] = $this->factory(function($container) {
            return new Execute(
                $container['properties']
            );
        });

        $this['input'] = $this->factory(function($container) {
            return new Input(
                $container['properties']
            );
        });

        $this['move'] = $this->factory(function($container) {
            return new Move(
                $container['properties']
            );
        });

        $this['output'] = $this->factory(function($container) {
            return new Output(
                $container['properties']
            );
        });

        $this['phpcodesniffer'] = $this->factory(function($container) {
            return new PhpCodeSniffer(
                $container['properties']
            );
        });

        $this['phpscript'] = $this->factory(function($container) {
            return new PhpScript(
                $container['properties']
            );
        });

        $this['property'] = $this->factory(function($container) {
            return new Property(
                $container['properties']
            );
        });

        $this['propertyfile'] = $this->factory(function($container) {
            return new PropertyFile(
                $container['properties']
            );
        });

        $this['tokenfilter'] = $this->factory(function($container) {
            return new TokenFilter(
                $container['properties']
            );
        });

        $this['touch'] = $this->factory(function($container) {
            return new Touch(
                $container['properties']
            );
        });
    }

}
