<?php

/**
 *
 * WordPress action hooks.
 *
 * @package Functions
 * @subpackage Action Hooks
 *
**/

// Prevent direct unless 'ajaxrequest' is set.
if(!empty($_SERVER['SCRIPT_FILENAME']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']) && !isset($_REQUEST['ajaxrequest'])){
    die('Sorry. This file cannot be loaded directly.');
}

/**
 * WPLG_add_assets
 * NULLED
 *
 * @param null
 * @return null
 * @since 1.0.0
 * @version 1.0.0
**/
function WPLG_add_assets(){
    wp_localize_script('WPLG-js', 'WPLG_add_assets_vars', array('pluginurl' => plugin_dir_url(__FILE__)));

    // JavaScript.
    wp_enqueue_script('WPLG-js-dashboard', WPLG_URL . 'templates/admin/dist/js/dashboard.js', false);

    // CSS.
    wp_enqueue_style('WPLG-css-dashboard', WPLG_URL . 'templates/admin/dist/css/dashboard.css', false);
    wp_enqueue_style('WPLG-css-cmb2', WPLG_URL . 'templates/admin/dist/css/cmb2.css');
}
add_action('admin_enqueue_scripts', 'WPLG_add_assets', 999);
