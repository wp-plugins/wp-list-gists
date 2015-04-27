<?php

/**
 *
 * WPLG_plugin_options.php
 *
 * @package Templates
 * @subpackage Options :: Plugin
 *
**/

// Prevent direct unless 'ajaxrequest' is set.
if(!empty($_SERVER['SCRIPT_FILENAME']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']) && !isset($_REQUEST['ajaxrequest'])){
    die('Sorry. This file cannot be loaded directly.');
}

global $metabox;

?>

<div class="wrap">
	<h1><?php _e('WP List Gists', 'wplg'); ?></h1>
	<p>You'll need to create a Personal API Access Token in your GitHub account and supply the token below. If you choose not to do this you can just supply a username but will be subject to the <a href="http://developer.github.com/v3/#unauthenticated-rate-limited-requests">unauthenticated rate limit</a>.</p>
	<h3>Using the WP List Gists plugin</h3>
	<p>After supplying a username all your Gists should be available as a list in your Posts, Pages and other post types. You can temporarily supply another username on a post by post basis to receive their Gists, just enter the username and the Gists will automatically update. Clicking any Gist from the list will add that Gist to your editor as a shortcode. (Please note, supplying other usernames is subject to <a href="http://developer.github.com/v3/#unauthenticated-rate-limited-requests">unauthenticated rate limit</a>.</p>
	<h3>Gist Shortcode</h3>
	<p>This plugin automatically creates a Gist shortcode. You can use the shortcode (with or without supplying the settings below) to retrieve any public Gists on GitHub like this: <pre>[gist user="username" id="gistid"]</pre></p>
</div>

<?php

foreach($metabox as $k=>$v){
	if(strpos($k, 'plugin_options') !== false){
		cmb2_metabox_form(WPLG_CMB2_PREFIX . $k, $k);
	}
}
