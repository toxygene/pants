<?php
require_once "Pants/Autoloader.php";

$pantsAutoloader = new Pants\Autoloader();
$pantsAutoloader->register();

require_once "Pile/Autoloader.php";

$pileAutoloader = new Pile\Autoloader();
$pileAutoloader->register();

require_once "Zend/Loader/Autoloader.php";

$zendAutoloader = Zend_Loader_Autoloader::getInstance();

$cli = new Pants\Cli();
$cli->run();
