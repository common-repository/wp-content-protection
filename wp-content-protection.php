<?php
/*
 * Plugin Name:WP Content Protection
 * Plugin uri:https://shankaranandmaurya.wordpress.com/my-developed-plugin/wp-content-protection/
 * Author:Shankaranand 
 * Description:This plugin(WP Content Protection : No membership required) is used to protect the content.Content protection is done by (i)-LoggedIn Security,(ii)-Password Security,(iii)-User Role security.
 * Version:1.3
 * Author uri:https://shankaranandmaurya.wordpress.com/
 * Text Domain:wpcp
 */

/*
 * About plugin.
 * We need the three types of the security.
 * 1: Access only logged in user      :Key - wpcp_logedin_protection
 * 2: Make this password protection   :Key - wpcp_password_protection
 * 3: Make this user roles protection :Key - wpcp_userroles_protection
 */
// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define('WPCP__PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPCP__PLUGIN_DIR', plugin_dir_path(__FILE__));

class wpcp_plugin_init {

    public function __construct() {
        ob_start();
        add_action('init', array($this, 'initialize_plugin_key_file'));
    }

    function initialize_plugin_key_file() {
        /*
         * Constant, constant value etc
         */
        require_once WPCP__PLUGIN_DIR . 'include/wpcp-constant.php';

        /*
         * Metabox for showing the content protection graphics to the user
         * it have the HTML security form on post,page or any CPT
         */
        include_once WPCP__PLUGIN_DIR . 'include/wpcp-initfunctions.php';

        /*
         * WPCP functions .
         */
        require_once WPCP__PLUGIN_DIR . 'include/wpcp-functions.php';

        /*
         * Content protection mechanism
         */
        require_once WPCP__PLUGIN_DIR . 'include/wpcp-content-protection-mechanism.php';
        /*
         * Generate shortcode mechanism
         */
        require_once WPCP__PLUGIN_DIR . 'include/wpcp-generate-shortcode-mechanism.php';
    }

}

new wpcp_plugin_init();