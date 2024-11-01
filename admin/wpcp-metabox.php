<?php
/*
 * This metabox is used to provide the graphics information for the admin for doing the settings.
 */

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function myplugin_add_meta_box() {

    $wpcp_functions = new wpcpfunctions();
    //get available selected post type from => settings -> wp content protection
    $selected_postType = get_option('wpcp_post_type_settings');
    //print_r($selected_postType);

    $screens = (!empty($selected_postType)) ? $selected_postType : array('post', 'page');

    foreach ($screens as $screen) {

        add_meta_box('wpcp_viv_metabox', __('WP Content Protection', 'wpcp'), 'wpcp_meta_box_callback_v', $screen, 'side');
    }
}

add_action('add_meta_boxes', 'myplugin_add_meta_box');

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function wpcp_meta_box_callback_v($post) {
$wpcp_functions = new wpcpfunctions();
    // Add an nonce field so we can check for it later.
    wp_nonce_field('wpcp_meta_box', 'wpcp_meta_box_nonce');

    /*
     * Use get_post_meta() to retrieve an existing value
     * from the database and use the value for the form.
     */
    
    $userroles = get_post_meta($post->ID, 'wpcp_isprotection_userroles',TRUE);//print_r($userrolesArr);
    
    $loggedin  = get_post_meta($post->ID, 'wpcp_isprotection_loggedin',TRUE);
    $passwordpt= get_post_meta($post->ID, 'wpcp_isprotection_password',TRUE);
    $passwordvl=get_post_meta($post->ID, 'wpcp_isprotection_passvalue',TRUE);
    ?>
    <!-- Here is the list of features which is used for protections  -->
    <h3>Select Protection Mechanism</h3>
    <!--  1st protection -->
    <p class="form-field">
        <input style="width:5px;" type="checkbox" id="make-this-content-private" name="make_this_content_private" value="1" <?php echo ($loggedin==1)? 'checked':""; ?> />
        <label for="make-this-content-private"><?php _e('Access only logged in user','wpcp');?></label>
    </p>
    <!-- 2nd protection -->
    <p class="form-field password-protected" >
        <input style="width:5px;" type="checkbox" class="checkbox_check" id="make-this-password-private" name="make_this_password_private" value="1" <?php echo ($passwordpt==1)? 'checked':""; ?> />
        <label for="make-this-password-private"><?php _e('Make this password protection','wpcp');?></label>
    </p>
    <p class="form-field form-password-field" style="display:<?php echo ($passwordpt==1)? 'inline-block':'none'; ?>">
        <label for="wpcp-content-password"><?php _e('Enter the password:','wpcp');?></label>
        <input type="password" name="wpcp_content_password" id="wpcp-content-password" value="<?php echo $passwordvl; ?>">
    </p>    
    <!-- 3rd protection -->
    <?php
    
    $availableRoles = $wpcp_functions->wpcp_get_role_names();
    ?>
    <p class="form-field">
        <label for="make-this-userroles-private"><?php _e('Make this user roles protection','wpcp');?></label>
        <select class="userroles" id="make-this-userroles-private" name="make_this_userroles_private" >
            <option value="">Select Roles</option>
            <?php
            
            if (count($availableRoles) > 0) {
                foreach ($availableRoles as $rk => $rv) {
                    
                    $checkedText=($userroles==$rk)?"selected='selected'":"";                 
                    
                    ?>
            <option   value="<?php echo $rk; ?>" <?php echo $checkedText; ?>  ><?php echo $rv; ?></option>   
                    <?php
                }
            }//if count functionality
            ?>
        </select>    
    </p>     
    <?php
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function wpcp_save_meta_box_data($post_id) {

    /*
     * We need to verify this came from our screen and with proper authorization,
     * because the save_post action can be triggered at other times.
     */

    // Check if our nonce is set.
    if (!isset($_POST['wpcp_meta_box_nonce'])) {
        return;
    }

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['wpcp_meta_box_nonce'], 'wpcp_meta_box')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    /* OK, it's safe for us to save the data now. */
    update_post_meta($post_id,'wpcp_isprotection_userroles',sanitize_text_field($_POST['make_this_userroles_private']));//print_r($userrolesArr);
    update_post_meta($post_id,'wpcp_isprotection_loggedin',intval($_POST['make_this_content_private']));
    update_post_meta($post_id,'wpcp_isprotection_password',intval($_POST['make_this_password_private']));
    update_post_meta($post_id,'wpcp_isprotection_passvalue',sanitize_text_field($_POST['wpcp_content_password']));
    
}

add_action('save_post', 'wpcp_save_meta_box_data');?>