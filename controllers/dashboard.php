<?php

/**
 *
 * Dashboard controller.
 *
 * @package Controllers
 * @subpackage Dashboard
 *
**/

// Namespace.
namespace WPLG\Controllers;

// Prevent direct unless 'ajaxrequest' is set.
if(!empty($_SERVER['SCRIPT_FILENAME']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']) && !isset($_REQUEST['ajaxrequest'])){
    die('Sorry. This file cannot be loaded directly.');
}

// Define class.
class Dashboard{

	/**
	 * __construct
	 * Constructor for this class.
	 *
	 * @access public
	 * @param null
	 * @return null
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function __construct(){
		// Add pages.
        add_action('admin_menu', array($this, 'create_wp_pages'));
	}

    /**
     * method_name
     * NULLED
     *
     * @access public
     * @param null
     * @return null
     * @since 1.0.0
     * @version 1.0.0
    **/
	public function create_wp_pages(){
		$custom_pages['plugin_parent'] = add_menu_page(
			__('WP List Gists', 'wplg'), // Menu title
			__('WP List Gists', 'wplg'), // Menu title
			'manage_options', // Permissions
			'WPLG_plugin_options', // Unique slug
			array($this, 'set_page') // Callback view
        );
	}

    /**
     * method_name
     * NULLED
     *
     * @access public
     * @param null
     * @return null
     * @since 1.0.0
     * @version 1.0.0
    **/
	public function set_page(){
		$view = new \WPLG\Views\Dashboard;

		return (isset($_GET['page'])) ? $view::fetch($_GET['page']) : false;
	}
}
