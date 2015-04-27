<?php

/**
 *
 * WordPress plugin shortcode.
 *
 * @package Functions
 * @subpackage Shortcode
 *
**/

// Prevent direct unless 'ajaxrequest' is set.
if(!empty($_SERVER['SCRIPT_FILENAME']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']) && !isset($_REQUEST['ajaxrequest'])){
    die('Sorry. This file cannot be loaded directly.');
}

/**
 * wp_lists_gists_shortcode
 * NULLED
 *
 * @param null
 * @return null
 * @since 1.0.0
 * @version 1.0.0
**/
function wp_lists_gists_shortcode($atts, $content = null){
	// Extract shortcode parameters.
    $atts = shortcode_atts(array(
        'dynamic' => false,
        'user'    => '',
        'id'      => ''
	), $atts, 'gist');

    // Get Gist from GitHub.
    $gist = json_decode(file_get_contents('https://gist.github.com/' . $atts['user'] . '/' . $atts['id'] . '.json'));

    if($atts['dynamic']){
        // Echo out the stylesheet if this needs to be dynamically added with JS.
        echo '<link rel="stylesheet" href="' . $gist->stylesheet . '">';
    }
    else{
        // Enqueue correctly if not.
        wp_enqueue_style($atts['id'], '' . $gist->stylesheet);
    }

    // CSS Fixes.
    echo '<style>.gist .line-numbers {width: 45px;}</style>';

	return $gist->div;
}
add_shortcode('gist', 'wp_lists_gists_shortcode');
