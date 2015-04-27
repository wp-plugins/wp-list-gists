<?php

/**
 *
 * Standard model for database interaction.
 *
 * @package Models
 * @subpackage Model
 *
**/

// Namespace.
namespace WPLG\Models;

// Prevent direct unless 'ajaxrequest' is set.
if(!empty($_SERVER['SCRIPT_FILENAME']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']) && !isset($_REQUEST['ajaxrequest'])){
    die('Sorry. This file cannot be loaded directly.');
}

// Define class.
class Model{

	/**
	 * method_name
	 * A description of the method.
	 *
	 * @access public
	 * @param null
	 * @return null
	 * @since 1.0.0
	 * @version 1.0.0
	**/
	public function method_name(){
	}
}
