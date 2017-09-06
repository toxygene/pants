<?php
/**
 * Pants
 *
 * Copyright (c) 2011-2017, Justin Hendrickson
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

namespace Pants\Cli;

use Doctrine\Common\Annotations\AnnotationRegistry;
use GetOpt\ArgumentException;
use GetOpt\GetOpt;
use GetOpt\Operand;
use GetOpt\Option;
use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\SerializerBuilder;
use Pants\Jms\CollectionsHandler;
use Pants\Project;
use Pants\Task\AbstractTask;
use Pants\Task\Call;
use Pants\Task\Chdir;
use Pants\Task\Chgrp;
use Pants\Task\Chmod;
use Pants\Task\Chown;
use Pants\Task\Copy;
use Pants\Task\Delete;
use Pants\Task\Execute;
use Pants\Task\FileSet;
use Pants\Task\Input;
use Pants\Task\Move;
use Pants\Task\Output;
use Pants\Task\PhpScript;
use Pants\Task\Property;
use Pants\Task\PropertyFile;
use Pants\Task\Symlink;
use Pants\Task\TokenFilter;
use Pants\Task\Touch;

/**
 * Runner
 *
 * @package Pants\Cli
 */
class Runner
{

    /**
     * Run the cli
     *
     * @var array $argv
     */
    public function run($argv)
    {
        $opt = new GetOpt(
            [
                Option::create('h', 'help', GetOpt::NO_ARGUMENT)
                    ->setDescription('Show the help text'),
                Option::create('f', 'file', GetOpt::REQUIRED_ARGUMENT)
                    ->setDescription('Set the build file')
                    ->setDefaultValue('build.xml'),
                Option::create('l', 'list', GetOpt::NO_ARGUMENT)
                    ->setDescription('Print a list of targets from the build file'),
                Option::create('v', 'version', GetOpt::NO_ARGUMENT)
                    ->setDescription('Print the version number')
            ]
        );

        $opt->addOperand(new Operand('targets', GetOpt::MULTIPLE_ARGUMENT));

        try {
            $opt->process();
        } catch (ArgumentException $exception) {
            file_put_contents('php://stderr', $exception->getMessage() . PHP_EOL);
            echo PHP_EOL . $opt->getHelpText();
            exit;
        }

        if ($opt->getOption('help')) {
            echo $opt->getHelpText();
            exit;
        }

        $buildFile = $opt->getOption('file');
        if (!file_exists($buildFile)) {
            echo "Build file \"{$buildFile}\" does not exist" . PHP_EOL;
            exit(1);
        }

        if (!preg_match('#.*\.(.*?)$#', $buildFile, $matches)) {
            exit(2);
        }

        AnnotationRegistry::registerLoader('class_exists');

        $builder = SerializerBuilder::create();
        $builder->configureHandlers(function(HandlerRegistry $handlerRegistry) {
            $handlerRegistry->registerSubscribingHandler(new CollectionsHandler());
        });

        $serializer = $builder->build();

        $taskClasses = [
            'call' => Call::class,
            'chdir' => Chdir::class,
            'chgrp' => Chgrp::class,
            'chmod' => Chmod::class,
            'chown' => Chown::class,
            'copy' => Copy::class,
            'delete' => Delete::class,
            'execute' => Execute::class,
            'fileset' => FileSet::class,
            'input' => Input::class,
            'move' => Move::class,
            'output' => Output::class,
            'php-script' => PhpScript::class,
            'property' => Property::class,
            'property-file' => PropertyFile::class,
            'symlink' => Symlink::class,
            'token-filter' => TokenFilter::class,
            'touch' => Touch::class
        ];

        foreach ($taskClasses as $name => $class) {
            $classMetadata = new ClassMetadata($class);
            $classMetadata->setDiscriminator('type', [$name => $class]);
            $classMetadata->discriminatorDisabled = false;

            $serializer->getMetadataFactory()
                ->getMetadataForClass($class)
                ->merge($classMetadata);
        }

        $classMetadata = new ClassMetadata(AbstractTask::class);
        $classMetadata->setDiscriminator('type', $taskClasses);
        $classMetadata->discriminatorDisabled = false;

        $serializer->getMetadataFactory()
            ->getMetadataForClass(AbstractTask::class)
            ->merge($classMetadata);

        /** @var Project $project */
        $project = $serializer->deserialize(
            file_get_contents($buildFile),
            Project::class,
            $matches[1]
        );

        if ($opt->getOption('list')) {
            $maxWidth = 0;
            foreach ($project->getTargets()->getDescriptions() as $name => $description) {
                $maxWidth = max($maxWidth, strlen($name));
            }

            foreach ($project->getTargets()->getDescriptions() as $name => $description) {
                printf("%{$maxWidth}s\t%s", $name, $description);
            }

            echo PHP_EOL;
            exit;
        }

        $project->execute($opt->getOperands());
    }
}
