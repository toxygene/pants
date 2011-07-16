<?php
require_once "/home/jhendric/zf2/library/Zend/Loader/StandardAutoloader.php";

use Pants\Project,
    Zend\Console\Getopt,
    Zend\Console\Exception as ConsoleException,
    Zend\Loader\StandardAutoloader;

$autoloader = new StandardAutoloader();
$autoloader->registerNamespace("Pants", "./Pants")
           ->register();

try {
    $opts = new Getopt(
        array(
            "file|f=s"  => "Set the build file (defaults to build.xml)",
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

if ($opts->getOption("f")) {
    $project->setBuildFile($opts->getOption("f"));
}
