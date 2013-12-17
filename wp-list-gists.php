<?php

/*
Plugin Name:WP List Gists
Plugin URI:https://github.com/WP-List-Gists
Description:Lists all Gists in your Posts and Pages from a given username and allows you to add them to your content in a shortcode form. The shortcode is also available for any other Github Gists, just supply a ID and User.
Version:1.1.1
Author:Stewart Dellow
Author URI:http://www.hellostew.com
GitHub Gists API:http://developer.github.com/v3/gists/#list-gists
Users Gists:https://api.github.com/users/username/gists
*/

$required_php_version = '5.3'; $required_wp_version = '3.5';

if(version_compare(PHP_VERSION, $required_php_version, '<') || version_compare(get_bloginfo('version'), $required_wp_version, '<')){
    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    deactivate_plugins(basename(__FILE__), true);

    if(isset($_GET['action']) && ($_GET['action'] == 'activate' || $_GET['action'] == 'error_scrape')){
        die(__("WP List Gists requires PHP version " . $required_php_version . " or greater and WordPress " . $required_wp_version . " or greater.", 'vab_core'));
    }
}

/**
 * initWpListGists
 *
 * @since 1.0.0
 * @version 0.1.0
 * @uses trailingslashit(), plugin_dir_path()
**/
function initWpListGists(){
    // Core
    require_once(trailingslashit(plugin_dir_path(__FILE__)) . 'wp-list-gists.class.php');
}
if(!class_exists('wpListGists')){
    add_action('plugins_loaded', 'initWpListGists');
}

?>
