<?php
/**
 * StatelessCMS.php
 * 
 * @package StatelessCMS
 * @version 1.4.2
 */

/**
 * Autoload module files
 * 
 * @param string $load Class name to load
 */
spl_autoload_register(function($load) {
    if (strpos($load, "Stateless\\") !== false) {
        $load = str_replace("\\", "/", $load);
        $load = str_replace("Stateless", "StatelessCMS", $load);
        include_once(__DIR__ . "/" . $load . ".php");
    }
});
