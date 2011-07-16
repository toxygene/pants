<?php
set_include_path(".:/usr/share/php:../library");

function autoloader($className) {
    require_once str_replace(array("\\", "_"), "/", $className) . ".php";
};

spl_autoload_register("autoloader");
