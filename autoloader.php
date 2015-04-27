<?php

/**
 *
 * PSR-0 Autoloader
 *
**/

// Prevent direct unless 'ajaxrequest' is set.
if(!empty($_SERVER['SCRIPT_FILENAME']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']) && !isset($_REQUEST['ajaxrequest'])){
    die('Sorry. This file cannot be loaded directly.');
}

// PSR-0 Autoloader
class WPLG_Autoloader{

    /**
     *
     * loader
     *
     * @access public
     * @param null
     * @return null
     * @since 1.0.0
     * @version 1.0.0
    **/
    static public function loader($class){
        // Check namespace.
        if(strpos($class, 'WPLG\\') === false) return;
        // File name.
        $file_name = str_replace('\\', DIRECTORY_SEPARATOR, strtolower($class) . '.php');
        // Include file.
        include(str_replace('wplg/', '', $file_name));
    }
}

spl_autoload_register('WPLG_Autoloader::loader');
