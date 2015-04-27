<?php

/**
 *
 * Handles anything and everything to do with CMB2.
 *
 * @package Functions
 * @subpackage CMB2
 *
**/

// Prevent direct unless 'ajaxrequest' is set.
if(!empty($_SERVER['SCRIPT_FILENAME']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']) && !isset($_REQUEST['ajaxrequest'])){
    die('Sorry. This file cannot be loaded directly.');
}

if(file_exists(WPLG_PATH . 'vendors/cmb2/init.php')){
	require_once WPLG_PATH . 'vendors/cmb2/init.php';
}

/**
 * cmb2_wplg
 * Retrieves custom CMB2 data. Will return either custom post meta or meta from options page.
 *
 * Post META: cmb2_wplg('status', 56);
 * Options META: cmb2_wplg('message_1', 'plugin_options');
 *
 * @param null
 * @return null
 * @since 1.0.0
 * @version 1.0.0
**/
function cmb2_wplg($key, $ID = null){
	if(!is_numeric($ID)){
		$options = get_option($ID);
		return (isset($options[WPLG_CMB2_PREFIX . $key])) ? $options[WPLG_CMB2_PREFIX . $key] : false;
	}
	elseif(is_numeric($ID)){
		return (get_post_meta($ID, WPLG_CMB2_PREFIX . $key)) ? get_post_meta($ID, WPLG_CMB2_PREFIX . $key, true) : false;
	}
}

/**
 * rrh_cmb_render_htmlarea
 * NULLED.
 *
 * @param null
 * @return null
 * @since 1.0.0
 * @version 1.0.0
**/
function cmb2_render_callback_for_htmlarea($field, $escaped_value, $object_id, $object_type, $field_type_object){
    if(is_array($field->args['content']) && !isset($field->args['content']['message'])){
    	if($field->args['content']){
	        echo '<ul class="wplg__gist-list">';
	        foreach($field->args['content'] as $gist){
	        	if(!empty($gist['id'])){
		            $title = ($gist['description']) ? $gist['description'] : $gist['id'];
		            echo '<li><a href="' . $gist['url'] . '" class="js-add-gist" data-gist-user="' . $gist['owner']['login'] . '" data-gist-id="' . $gist['id'] . '">' . $title . '</a></li>';
	        	}
	        }
	        echo '</ul>';
    	}
    }
    elseif($field->args['content']['message'] !== 'Not Found'){
        printf(__('No data has been found. Did you <a href="%s">set up</a> the plugin correctly?', 'wplg'), WPLG_URL + '?page=WPLG_plugin_options');
    }
    else{
    	_e('Unknown error', 'wplg');
    }
}
add_action('cmb2_render_htmlarea', 'cmb2_render_callback_for_htmlarea', 10, 5);

/**
 * cmb2_WPLG_product_metaboxes
 * NULLED.
 *
 * @param null
 * @return null
 * @since 1.0.0
 * @version 1.0.0
**/
function cmb2_WPLG_product_metaboxes(){
	global $metabox;

	// Posttype Options.
	$metabox['posttype_options'] = new_cmb2_box(array(
		'id'           => WPLG_CMB2_PREFIX . 'option_section',
		'title'        => __('WP List Gists', 'wplg'),
		'object_types' => cmb2_wplg('post_types', 'plugin_options'),
		'context'      => 'side',
		'priority'     => 'default',
		'cmb_styles'   => false
	));

	// Posttype :: GitHub Username
	$metabox['posttype_options']->add_field(array(
		'name' => __('GitHub Username', 'wplg'),
		'desc' => __('', 'wplg'),
		'id'   => WPLG_CMB2_PREFIX . 'gists_username',
		'type' => 'text'
	));
	// Posttype Options :: Option
	$metabox['posttype_options']->add_field(array(
		'name'    => __('', 'wplg'),
		'desc'    => __('', 'wplg'),
		'id'      => WPLG_CMB2_PREFIX . 'gists',
		'type'    => 'htmlarea',
		'content' => WPLG_get_gists()
	));


	// Plugin Options
	$metabox['plugin_options'] = new_cmb2_box(array(
		'name'    => __('WP List Gists Settings', 'wplg'),
		'id'      => WPLG_CMB2_PREFIX . 'plugin_options',
		'hookup'  => false,
		'show_on' => array(
			'key'   => 'options-page',
			'value' => array('plugin_options')
		),
	));

	// Plugin Options :: GitHub Username
	$metabox['plugin_options']->add_field(array(
		'name' => __('GitHub Username', 'wplg'),
		'desc' => __('', 'wplg'),
		'id'   => WPLG_CMB2_PREFIX . 'github_username',
		'type' => 'text'
	));
	// Plugin Options :: GitHub Token
	$metabox['plugin_options']->add_field(array(
		'name' => __('GitHub Token', 'wplg'),
		'desc' => __('', 'wplg'),
		'id'   => WPLG_CMB2_PREFIX . 'github_token',
		'type' => 'text'
	));
	// Plugin Options :: Posttypes
	$metabox['plugin_options']->add_field(array(
		'name'    => __('Post Types', 'wplg'),
		'desc'    => __('Select the post types to apply the plugin too.', 'wplg'),
		'id'      => WPLG_CMB2_PREFIX . 'post_types',
		'type'    => 'multicheck',
		'options' => WPLG_get_post_types()
	));
}
add_action('cmb2_init', 'cmb2_WPLG_product_metaboxes');

/**
 * cmb2_WPLG_options_layout
 * Modifies the result of cmb2_metabox_form().
 *
 * @param null
 * @return null
 * @since 1.0.0
 * @version 1.0.0
**/
function cmb2_WPLG_options_layout($form_format, $object_id, $cmb){
	global $metabox;

	foreach($metabox as $k=>$v){
		if(strpos($k, 'plugin_options') !== false){
			$layouts[] = $k;
		}
	}

    if(in_array($object_id, $layouts)){
    	$name = (!empty($cmb->meta_box['name'])) ? $cmb->meta_box['name'] : ucwords(str_replace('_', ' ', $object_id));
    	$form_format  = '';
    	$form_format .= '<div class="wrap metabox-wrap cf">';
    	$form_format .= '<h2>' . $name . '</h2>';
        $form_format .= '<form class="cmb-form" method="post" id="%1$s" enctype="multipart/form-data" encoding="multipart/form-data"><input type="hidden" name="object_id" value="%2$s">%3$s<div class="submit-wrap"><input type="submit" name="submit-cmb" value="' . __('Save Options', 'wplg') . '" class="button-primary"></div></form>';
    	$form_format .= '</div>';
    }

    return $form_format;
}
add_filter('cmb2_get_metabox_form_format', 'cmb2_WPLG_options_layout', 10, 3);
