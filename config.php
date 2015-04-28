<?php

/**
 *
 * Configuration and settings.
 *
 * @package Index
 * @subpackage Config
 *
**/

// Prevent direct.
if(!empty($_SERVER['SCRIPT_FILENAME']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])){
    die('Sorry. This file cannot be loaded directly.');
}

// Plugin version.
define('WPLG_PLG_VERSION', '1.2.1');
// Plugin name.
define('WPLG_PLG_NAME', 'WP List Gists');
// Plugin directory.
define('WPLG_PATH', plugin_dir_path(__FILE__));
// Plugin URL.
define('WPLG_URL', plugin_dir_url(__FILE__));

// CMB2 Prefix.
define('WPLG_CMB2_PREFIX', '_WPLG_cmb2_');

// 1. Load autoloader.
require_once(WPLG_PATH . 'autoloader.php');
// 2. Misc functions.
require_once(WPLG_PATH . 'functions/misc.php');
// 3. Load actions.
require_once(WPLG_PATH . 'functions/actions.php');
// 4. Load shortcode.
require_once(WPLG_PATH . 'functions/shortcode.php');
// 5. Load CMB2 actions & filters.
require_once(WPLG_PATH . 'functions/cmb2.php');
