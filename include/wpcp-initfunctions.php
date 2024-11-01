<?php
/* 
 *Init functionality of the plugin.
 */

/* 
 *This functions is used to provide the initialization functionality .
 */
class wpcpinitfunctions{
    
    function __construct() {
      add_action('admin_enqueue_scripts',array($this,'wpcp_admin_scripts_styles'));
      add_action('wp_enqueue_scripts',array($this,'wpcp_scripts_styles'));
      //wp-admin
      //Create an menu for settings.
      //1: Settings => wp content protection.
        add_action('admin_menu',array($this,'wpcp_callback_admin_menu'));
      
     //create metabox for selecting post type.
        $this->wpcp_metabox_settings();
    }
    /*
     *Adding the scripts and style in admin 
     */
    function wpcp_admin_scripts_styles(){
        wp_enqueue_script('adminwpcpscriptsjs', WPCP__PLUGIN_URL.'assets/js/admin-wpcp-script.js',array('jquery'),'',TRUE);
        wp_enqueue_style('adminwpcpscriptscss', WPCP__PLUGIN_URL.'assets/css/admin-wpcp-style.css');
    }
    
    function wpcp_scripts_styles(){
        wp_enqueue_style('wpcp.style.css', WPCP__PLUGIN_URL.'assets/css/wpcp-style.css');
    }
    
    //***************************  Settings => wp content protection **************

    function wpcp_callback_admin_menu(){
     add_submenu_page( 'options-general.php', 'WP Content Protection', 'WP Content Protection', 'manage_options', 'wp-content-protection', array($this,'wpcp_settings_page_callback') );
    }
    
    /*
     *Submenu content settings.
     */
    function wpcp_settings_page_callback(){
     //plugin settings options in setting => wp content protection
     require_once WPCP__PLUGIN_DIR.'admin/wpcp-setting-options.php';   
    }

    //***************************  Settings => wp content protection END **************
    
   //************************** Creating metabox ***********************
   function wpcp_metabox_settings(){
       require_once WPCP__PLUGIN_DIR.'admin/wpcp-metabox.php';   
   } 
    
    
}//class init functions
new wpcpinitfunctions();