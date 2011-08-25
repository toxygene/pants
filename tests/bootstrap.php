<?php
set_include_path(".:/usr/share/php");

require_once "../library/Pants/Autoloader.php";

$pantsAutoloader = new Pants\Autoloader();
$pantsAutoloader->register();

require_once "../../pile/library/Pile/Autoloader.php";

$pileAutoloader = new Pile\Autoloader();
$pileAutoloader->register();

spl_autoload_register(function ($className) {
    if (preg_match("#^PHPUnit_#", $className)) {
        require_once str_replace("_", DIRECTORY_SEPARATOR, $className) . ".php";
    }
});
