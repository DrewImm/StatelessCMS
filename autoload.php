<?php
/**
 * autoload.php
 * 
 * @package StatelessCMS
 * @version 0.0.1
 */
session_start();

/**
 * @brief Autoload module files
 * @param string $load Class name to load
 */
spl_autoload_register(function($load) {
    if (strpos($load, "Stateless\\") !== false) {
        $load = str_replace("\\", "/", $load);
        $load = str_replace("Stateless", "lib", $load);
        include_once(__DIR__ . "/" . $load . ".php");
    }
});