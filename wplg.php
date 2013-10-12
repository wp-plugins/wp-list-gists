<?php

/**
 *
 * Plugin Name: WP List Gists
 * Plugin URI: https://github.com/sdellow/WP-List-Gists
 * Description: Lists all Gists in your Posts and Pages from a given username and allows you to add them to your content in a shortcode form. The shortcode is also available for any other Github Gists, just supply a ID and User.
 * Version: 1.0.0
 * Author: Stewart Dellow
 * Author URI: http://www.hellostew.com
 *
 * GitHub Gists API: http://developer.github.com/v3/gists/#list-gists
 * Users Gists: https://api.github.com/users/username/gists
 *
**/

require_once(ABSPATH .'wp-includes/pluggable.php');

class wpListGists{

    // Public variables
    public $plg_name, $plg_version, $plg_github, $pagename, $allGists, $successmessage, $errormessage;

    /**
     * Constructor
     * @since 1.0.0
     * @version 0.1.0
    **/
    public function __construct(){

        // Define the Plugin Name
        define('WPLG_NAME', 'WP List Gists');
        // Define the text domain
        define('WPLG_TEXT_DOMAIN', 'wplg');
        // Define the Plugin Version
        define('WPLG_VERSION', '1.0.0');
        // Define the Plugin URL
        define('WPLG_WEBSITE', 'https://github.com/sdellow/WP-List-Gists');
        // Define the plugin path
        define('WPLG_PATH', plugin_dir_path(__FILE__));
        // Define the plugin URL
        define('WPLG_URL', plugins_url(__FILE__));

        // Set the main page name
        $this->pagename = WPLG_TEXT_DOMAIN . '_list_all_gists';

        // Only run the following while editing a post
        if(is_admin() && (strstr($_SERVER['REQUEST_URI'], 'wp-admin/post-new.php') || strstr($_SERVER['REQUEST_URI'], 'wp-admin/post.php'))){
            // Set the GitHub username
            $this->allGists = (isset($_REQUEST['temp_github_username'])) ? $this->return_temp_gists() : $this->return_user_gists();

            // Add custom fields
            add_action('after_setup_theme', array(&$this, 'cmb_Meta_Box'));
            add_filter('cmb_meta_boxes', array(&$this, 'plg_custom_fields'));
            add_action('cmb_render_htmlarea', array(&$this, 'rrh_cmb_render_htmlarea'), 10, 2);
        }

        // Add menus
        add_action('admin_menu', array(&$this, 'add_page_menus'));
        // Add Backend CSS & JS
        add_action('admin_enqueue_scripts', array(&$this, 'add_backend_styles_scripts'));
        // Create shortcode
        add_shortcode('gist', array(&$this, 'create_shortcode'));

        // Actions
        if(isset($_POST['action'])){
            switch($_POST['action']){
                case 'save' :
                    $this->save_options();
                break;
            }
        }
    }

    /* ==========================================================================
       Plugin Specific Functions
    ========================================================================== */
    /**
     * Uses curl to get data from a passed URL
     * @since 1.0.0
     * @version 0.1.0
    **/
    public function getData($url, $methodPost = false, $postdata = null){
        // Check if curl is available
        if(!extension_loaded('curl')){
            throw new Exception('The PHP extension curl must be installed to use this library.', Exception::CURL_NOT_FOUND);
        }

        // Set the Authorization header
        $authorization = (get_option('wplg_github_token')) ? 'Authorization: token ' . get_option('wplg_github_token') : null;

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
            //throw new Exception('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
            return __("Could not retrieve Gists from GitHub", WPLG_TEXT_DOMAIN);
        }
        else{
            $json_response = curl_exec($curl);
            curl_close($curl);
            // Return our data array
            return json_decode($json_response, true);
        }
    }

    /**
     * Returns array of Gists from option username
     * @since 1.0.0
     * @version 0.1.0
    **/
    public function return_user_gists(){
        if(get_option('wplg_github_username')){
            $requestedGists = $this->getData('https://api.github.com/users/' . get_option('wplg_github_username') . '/gists');

            return (isset($requestedGists['message'])) ? $requestedGists['message'] : $requestedGists;
        }
        else{
            return __("No Username supplied", WPLG_TEXT_DOMAIN);
        }
    }

    /**
     * Returns array of Gists from temp username
     * @since 1.0.0
     * @version 0.1.0
    **/
    public function return_temp_gists(){
        $requestedGists = $this->getData('https://api.github.com/users/' . $_REQUEST['temp_github_username'] . '/gists');

        return (isset($requestedGists['message'])) ? $requestedGists['message'] : $requestedGists;
    }

    /**
     * Custom CMB field type
     * @since 1.0.0
     * @version 0.1.0
    **/
    public function rrh_cmb_render_htmlarea($field, $meta){
        if(is_array($field['content'])){
            echo '<ul class="gist-list">';
            foreach($field['content'] as $gist){
                $title = ($gist['description']) ? $gist['description'] : $gist['id'];
                echo '<li><a href="' . $gist['url'] . '" class="add-gist" data-gist-user="' . $gist['user']['login'] . '" data-gist-id="' . $gist['id'] . '">' . $title . '</a></li>';
            }
            echo '</ul>';
        }
        else{
            _e('Error: ' . $field['content'], WPLG_TEXT_DOMAIN);
        }
    }

    /* ==========================================================================
       Generic Functions
    ========================================================================== */
    /**
     * Retrieves all post types
     * @since 1.0.0
     * @version 0.1.0
    **/
    public function get_posttypes(){
        $this->pt_builtin = array('page', 'post');
        $this->pt_custom  = get_post_types(array('public' => true, '_builtin' => false), 'names');
        $this->pt_all     = array_merge($this->pt_custom, $this->pt_builtin);

        return $this->pt_all;
    }

    /**
     * Creates a shortcode for the plugin
     * @since 1.0.0
     * @version 0.1.0
    **/
    public function create_shortcode($atts){
       extract(shortcode_atts(array(
           'user' => '',
           'id'   => ''
       ), $atts));

        $gist = file_get_contents('https://gist.github.com/' . $user . '/' . $id . '.json');
        $json = json_decode($gist);

        // Echo out the stylesheet if this needs to be dynamically added with JS
        echo '<link rel="stylesheet" href="https://gist.github.com' . $json->stylesheet . '">';
        //wp_enqueue_style($id, 'https://gist.github.com' . $json->stylesheet);

        return $json->div;
    }

    /**
     * Adds the CMB Meta Box
     * @since 1.0.0
     * @version 0.1.0
    **/
    public function cmb_Meta_Box(){
        if(!class_exists('cmb_Meta_Box')){
            require_once(WPLG_PATH . 'cmb/init.php');
        }
    }

    /**
     * Creates the custom fields
     * @since 1.0.0
     * @version 0.1.0
    **/
    public function plg_custom_fields($meta_boxes){
        $prefix = WPLG_TEXT_DOMAIN . '_cf_';
        $meta_boxes[] = array(
            'id'         => str_replace('-', '_', sanitize_title_with_dashes(WPLG_NAME)),
            'title'      => WPLG_NAME,
            'pages'      => get_option('wplg_post_types'),
            'context'    => 'side',
            'priority'   => 'default',
            'show_names' => true,
            'fields'     => array(
                array(
                    'name' => __("GitHub Username", WPLG_TEXT_DOMAIN),
                    'desc' => __('You\'re username is set as <span class="' . $prefix . 'github_username_id"><strong>' . get_option('wplg_github_username') . '</strong></span>. To get Gists from a different user enter the username below', WPLG_TEXT_DOMAIN),
                    'id'   => $prefix . 'github_username',
                    'type' => 'text'
                ),
                array(
                    'name'    => __("Gists", WPLG_TEXT_DOMAIN),
                    'desc'    => '',
                    'id'      => $prefix . 'gists',
                    'type'    => 'htmlarea',
                    'content' => $this->allGists
                ),
            ),
        );

        return $meta_boxes;
    }

    /**
     * Adds menu item to Settings page
     * @since 1.0.0
     * @version 0.1.0
    **/
    public function add_page_menus(){
        // Main menu item
        add_options_page(WPLG_NAME, WPLG_NAME, 'manage_options', $this->pagename, array(&$this, 'display_data'));
    }

    /**
     * Adds the backend CSS and JS
     * @since 1.0.0
     * @version 0.1.0
    **/
    public function add_backend_styles_scripts($page){
        wp_enqueue_script('be-' . WPLG_TEXT_DOMAIN . '-js', plugins_url('be-' . WPLG_TEXT_DOMAIN . '.js', __FILE__), array('jquery'));
        wp_enqueue_script('heartcode-canvas-js', plugins_url('heartcode-canvasloader-min-0.9.js', __FILE__), array('jquery'));
        wp_enqueue_style('be-' . WPLG_TEXT_DOMAIN . '-css', plugins_url('be-' . WPLG_TEXT_DOMAIN . '.css', __FILE__));
    }

    /**
     * Notes on how to use the plugin
     * @since 1.0.0
     * @version 0.1.0
    **/
    public function documentation(){ ?>
        <h3><?php _e("Using the " . WPLG_NAME . " plugin", WPLG_TEXT_DOMAIN); ?></h3>
        <p>After supplying a username all your Gists should be available as a list in your Posts, Pages and other post types. You can temporarily supply another username on a post by post basis to receive their Gists, just enter the username and click out of the box, the Gists will automatically update. Clicking any Gist from the list will add that Gist to your editor as a shortcode.</p>
        <h3>Gist Shortcode</h3>
        <p>This plugin automatically creates a Gist shortcode. You can use the shortcode (regardless of the plugin settings) to retrieve any public Gists on GitHub like this: <span class="bold">[gist user="username" id="gistid"]</span>. The shortcode retrieves the Gist in a JSON format using PHP so you should be able to dynamically load Gists.</p>
    <?php }

    /**
     * Display the options form
     * @since 1.0.0
     * @version 0.1.0
    **/
    public function display_data(){
    ?>
        <div id="plg-id-<?php echo WPLG_TEXT_DOMAIN; ?>" class="cf">
            <div class="plgcontainer">
                <h2><?php _e(WPLG_NAME . " Settings", WPLG_TEXT_DOMAIN); ?></h2>
                <?php if(!empty($this->successmessage)) : ?>
                    <div class="updated fade" style="margin: 5px 0 20px;"><p><?php echo $this->successmessage; ?></p></div>
                <?php endif; ?>
                <?php if(!empty($this->errormessage)) : ?>
                    <div class="error fade" style="margin: 5px 0 20px;"><p><?php echo $this->errormessage; ?></p></div>
                <?php endif; ?>
                <p><?php _e("You'll need to create a Personal API Access Token in your GitHub account and supply the token below. If you choose not to do this you can just supply a username but will be subject to the <a href=\"http://developer.github.com/v3/#unauthenticated-rate-limited-requests\">unauthenticated rate limit</a>.", WPLG_TEXT_DOMAIN); ?></p>
                <form id="lag-form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                    <div class="field">
                        <div class="label-wrapper"><label for="wplg_github_username"><?php _e("GitHub Username", WPLG_TEXT_DOMAIN); ?></label></div>
                        <input type="text" name="wplg_github_username" id="wplg_github_username" value="<?php echo get_option('wplg_github_username'); ?>">
                    </div>
                    <div class="field">
                        <div class="label-wrapper"><label for="wplg_github_token"><?php _e("GitHub Token", WPLG_TEXT_DOMAIN); ?></label></div>
                        <input type="password" name="wplg_github_token" id="wplg_github_token" value="<?php echo get_option('wplg_github_token'); ?>">
                    </div>
                    <div class="field">
                        <div class="label-wrapper"><label for="wplg_post_types"><?php _e("Select Post Types to apply the plugin too", WPLG_TEXT_DOMAIN); ?></label></div>
                        <select name="wplg_post_types[]" id="wplg_post_types" multiple="multiple">
                            <?php foreach($this->get_posttypes() as $pt) : ?>
                                <option value="<?php echo $pt; ?>" <?php if(get_option('wplg_post_types') && in_array($pt, get_option('wplg_post_types'))) : echo 'selected="selected"'; endif; ?>><?php echo ucwords($pt); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php wp_nonce_field(plugin_basename(__FILE__), WPLG_TEXT_DOMAIN . '_form'); ?>
                    <input type="hidden" name="action" value="save">
                    <button type="submit" class="button-primary"><?php _e("Save Settings", WPLG_TEXT_DOMAIN); ?></button>
                </form>
                <hr>
                <?php $this->documentation(); ?>
            </div>
            <div class="credits">
                <h2><?php _e("Credits", WPLG_TEXT_DOMAIN); ?></h2>
                <p><span class="bold">Plugin Name:</span> <?php echo WPLG_NAME; ?></p>
                <p><span class="bold">Version:</span> <?php echo WPLG_VERSION; ?></p>
                <p><span class="bold">GitHub:</span> <a href="<?php echo WPLG_WEBSITE; ?>" target="_blank"><?php echo str_replace('https://', '', WPLG_WEBSITE); ?></a></p>
                <p><span class="bold">Author:</span> Stewart Dellow</p>
                <p><span class="bold">Website:</span> <a href="http://www.hellostew.com" target="_blank">www.hellostew.com</a></p>
            </div>
        </div>
    <?php }

    /**
     * Saves the options
     * @since 1.0.0
     * @version 0.1.0
    **/
    public function save_options(){
        if(wp_verify_nonce($_POST[WPLG_TEXT_DOMAIN . '_form'], plugin_basename(__FILE__))){
            if(isset($_POST['wplg_github_username'])){
                update_option('wplg_github_username', $_POST['wplg_github_username']);
            }
            if(isset($_POST['wplg_github_username'])){
                update_option('wplg_github_token', $_POST['wplg_github_token']);
            }
            if(isset($_POST['wplg_github_username'])){
                update_option('wplg_post_types', $_POST['wplg_post_types']);
            }

            $this->successmessage = __("Settings have been updated", WPLG_TEXT_DOMAIN);
        }
        else{
            $this->errormessage = __("Sorry, there was a problem please try again.", WPLG_TEXT_DOMAIN);
        }
    }

}

$wpListGists = new wpListGists();

?>
