<?php

/**
 *
 * Misc functions.
 *
 * @package Functions
 * @subpackage Misc
 *
**/

// Prevent direct unless 'ajaxrequest' is set.
if(!empty($_SERVER['SCRIPT_FILENAME']) && basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME']) && !isset($_REQUEST['ajaxrequest'])){
    die('Sorry. This file cannot be loaded directly.');
}

/**
 * output
 * Outputs based on environment.
 *
 * @since 1.0.0
 * @version 1.0.0
**/
if(!function_exists('output')){
    function output($input, $print = false){
        if(isset($_REQUEST['ajaxrequest'])){
            print json_encode($input);
            exit();
        }
        else{
            if($print){
                print_r($input);
            }
            else{
                return $input;
            }
        }
    }
}

/**
 * WPLG_get_post_types
 * Gets all post types from WP.
 *
 * @since 1.0.0
 * @version 1.0.0
**/
function WPLG_get_post_types(){
    $pt_custom = get_post_types(array('public' => true, '_builtin' => true), 'names');

    unset($pt_custom['attachment']);
    unset($pt_custom['revision']);
    unset($pt_custom['nav_menu_item']);

    return $pt_custom;
}

/**
 * WPLG_get_gists
 * Get gists from GitHub.
 *
 * @since 1.0.0
 * @version 1.0.0
**/
function WPLG_get_gists($methodPost = false, $postdata = null){
    $url = 'https://api.github.com/users/' . cmb2_wplg('github_username', 'plugin_options') . '/gists';

    // Check if curl is available
    if(!extension_loaded('curl')){
        throw new Exception('The PHP extension curl must be installed to use this library.', Exception::CURL_NOT_FOUND);
    }

    // Set the Authorization header
    $authorization = (cmb2_wplg('github_token', 'plugin_options')) ? 'Authorization: token ' . cmb2_wplg('github_token', 'plugin_options') : '';

    // Initiate curl
    $curl = curl_init($url);
    curl_setopt_array($curl, array(
        CURLOPT_URL            => $url,
        CURLOPT_HEADER         => false,
        CURLOPT_CONNECTTIMEOUT => 2,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT      => 'curl/' . $curl['version'],
        CURLOPT_FAILONERROR    => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => array(
            'Accept: application/json',
            'Content-type: application/json',
            $authorization
        )
    ));

    if($methodPost){
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
    }

    if(!curl_exec($curl)){
        return __("Could not retrieve Gists from GitHub", 'wplg');
    }
    else{
        $json_response = curl_exec($curl);
        curl_close($curl);

        // Return our data array
        return json_decode($json_response, true);
    }
}
