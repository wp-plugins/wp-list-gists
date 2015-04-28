<?php

/*
Plugin Name: WP List Gists
Plugin URI: https://github.com/wp-lists-gists
Version: 1.2.1
Description: Lists all Gists in your Posts and Pages from a given username and allows you to add them to your content in a shortcode form. The shortcode is also available for any other Github Gists, just supply a ID and User.
Author: Stew Dellow
Author URI: https://hellostew.com
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
Text Domain: wplg
*/

// Prevent direct access.
if(!defined('WPINC')){die;}

/**
 * init_wp_lists_gists
 * Init the plugin.
 *
 * @param null
 * @return null
 * @since 1.0.0
 * @version 1.0.0
**/
function init_wp_lists_gists(){
    // Do version checks.
    vc_wp_lists_gists('wp-lists-gists', 'wp-lists-gists');
    // Require config.
    require_once(plugin_dir_path(__FILE__) . 'config.php');
    // Init Dashboard.
    new \WPLG\Controllers\Dashboard;
}
add_action('plugins_loaded', 'init_wp_lists_gists');

/**
 * listen_wp_lists_gists
 * Listens to form post.
 *
 * @param null
 * @return null
 * @since 1.0.0
 * @version 1.0.0
**/
function listen_wp_lists_gists(){
    // Init Request.
    new \WPLG\Controllers\Request;
}
add_action('wp', 'listen_wp_lists_gists');

/**
 * vc_wp_lists_gists
 * Do version checks.
 *
 * @param null
 * @return null
 * @since 1.0.0
 * @version 1.0.0
**/
function vc_wp_lists_gists($name, $slug){
    // Version variables
    $required_php_version = '5.5'; $required_wp_version = '4.0';

    // Version checks
    if(version_compare(PHP_VERSION, $required_php_version, '<') || version_compare(get_bloginfo('version'), $required_wp_version, '<')){
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        deactivate_plugins(basename(__FILE__), true);

        if(isset($_GET['action']) && ($_GET['action'] == 'activate' || $_GET['action'] == 'error_scrape') && $_GET['plugin'] == $slug){
            die(__($name . ' requires PHP version ' . $required_php_version . ' or greater and WordPress ' . $required_wp_version . ' or greater.'));
        }
    }
}

/**
 * Activation
 *
 * @since 1.0.0
 * @version 1.0.0
**/
if(file_exists(plugin_dir_path(__FILE__) . 'controllers/activation.php')){
    require_once(plugin_dir_path(__FILE__) . 'controllers/activation.php');
    register_activation_hook(__FILE__, array('Activation', 'activate'));
}

/**
 * Deactivation
 *
 * @since 1.0.0
 * @version 1.0.0
**/
if(file_exists(plugin_dir_path(__FILE__) . 'controllers/deactivation.php')){
    require_once(plugin_dir_path(__FILE__) . 'controllers/deactivation.php');
    register_activation_hook(__FILE__, array('Deactivation', 'deactivate'));
}
