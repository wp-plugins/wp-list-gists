<?php

/**
 *
 * Listens for POST/GET requests and calls appropriate model.
 *
 * @package Controllers
 * @subpackage Request
 *
**/

// Namespace.
namespace WPLG\Controllers;

// Prevent direct unless 'ajaxrequest' is set.
if(!empty($_SERVER['SCRIPT_FILENAME']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']) && !isset($_REQUEST['ajaxrequest'])){
    die('Sorry. This file cannot be loaded directly.');
}

// Define class.
class Request{

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
	}
}
