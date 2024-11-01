<?php
/*
 * Settings options file
 * Settins => wp content protection
 */
//************************************* works when form will submit *********************************
//Form submission : Settings => post type
if(isset($_POST['formOption'])){
if ($_POST['formOption'] == "setting_postType") {

    //update the post into the options table.
    update_option('wpcp_post_type_settings',$_POST['wpcp_postsettings']);
    //after saving the value it will redirect on page

    $redirectURL = sanitize_text_field($_POST['redirect']);
    wp_redirect($redirectURL);
    exit;
}
}
//Form submission : Settings => protection and wrong message .
if(isset($_POST['formOption'])){
if ($_POST['formOption'] == "setting_msg_option") {

    //update the post into the options table.
    update_option('wpcp_content_protection_msg',  stripslashes($_POST['wpcp_cnt_ptn_msg']));
    update_option('wpcp_wrongpass_msg',  stripslashes($_POST['wpcp_wrongpass_msg']));
    //after saving the value it will redirect on page
    $redirectURL = sanitize_text_field($_POST['redirect']);
    wp_redirect($redirectURL);
    exit;
  }
}
//************************************* works when form will submit *********************************
?>
<div class="wrap">
    <h2><?php _e('WP Content Protection', 'wpcp'); ?></h2>

    <!--  ===========================Save post type start =============================================  --> 
    <div class="settings-area">
        <label><b><?php _e('Select the post type for which you need to content protection.', 'wpcp'); ?></b></label>
        <form method="POST" action="">

            <input type="hidden" value="setting_postType" name="formOption" />
            <input type="hidden" value="<?php echo admin_url('options-general.php?page=wp-content-protection'); ?>" name="redirect" />
            <?php
            $blockArray = unserialize(BLOCKED_POST_TYPE);//print_r($blockArray);
            $objFun = new wpcpfunctions();
            $arrPosttype = $objFun->wpcp_get_available_post_type();

            //get saved setting value.
            $ischecked = "";
            $saved_posttype = get_option('wpcp_post_type_settings');
            if(empty($saved_posttype)){
             $saved_posttype =array('page','post');
             update_option('wpcp_post_type_settings',$saved_posttype);
             }
            //print_r($saved_posttype);

            if (count($arrPosttype) > 0) {
                foreach ($arrPosttype as $arrtype) {

                   // $type = $arrtype->post_type;
                      $type = $arrtype;
                    //Blocked post type
                    if (in_array($type, $blockArray)) {
                        continue;
                    }
                     if(is_array($saved_posttype)>0){
                       $ischecked = (in_array($type, $saved_posttype)) ? "checked" : "";
                     }
                    ?>
                    <p class="form-field">
                        <input style="width:10px;" type="checkbox" id="<?php echo $type; ?>"  name="wpcp_postsettings[]" value="<?php echo $type; ?>" title="<?php echo $type; ?>" <?php echo $ischecked; ?> > 
                        <label for="<?php echo $type; ?>"><b> <?php echo ucfirst($type); ?></b></label>
                    </p>   
                    <?php
                }//end of the foreach 
            }//if count functions
            ?>   
            <p class="form-field-submit">
                <input  class="button submit" type="submit" value="Save Changes" name="wpcp_setting_submit">
            </p>  
        </form>  
    </div>
    <!--  ===========================Save post type start End =============================================  --> 
    <!--  ===========================Message body start here =============================================  --> 

    <div class="msgbody">
        <form method="POST" action="" > 

            <input type="hidden" value="setting_msg_option" name="formOption" />
            <input type="hidden" value="<?php echo admin_url('options-general.php?page=wp-content-protection'); ?>" name="redirect" />

            <?php
            $protection_msg = get_option('wpcp_content_protection_msg');
            $wrongPassMsg = get_option('wpcp_wrongpass_msg');
//default initialization.  
            $default_init_protection_msg = SETTING_PROTECTION_MSG;
            if (empty($protection_msg)) {
                $protection_msg = $default_init_protection_msg;
            }
//wrong pass msg
            $default_init_wrongpass_msg = SETTING_WRONGPASS_MSG;

            if (empty($wrongPassMsg)) {
                $wrongPassMsg = $default_init_wrongpass_msg;
            }
            ?>    

            <p class="form-field">
                <label for="wpcp_cnt_ptn_msg" style="width:100%;"><b><?php _e("Enter the protection message","wpcp");?></b></label>
            </p>

            <div class="textarea">
                <p class="form-field">
                    <span class="msg-shortcode"><?php _e('For showing the available security use the shortcode','wpcp');?> :"{wpcp_security_level}"</span>
                    <textarea rows="5" cols="70" name="wpcp_cnt_ptn_msg" id="wpcp_cnt_ptn_msg" ><?php echo $protection_msg; ?></textarea>
                </p>
            </div>

            <div class="textarea">
                <p class="form-field">
                    <label for="wpcp_wrongpass_msg"><b><?php _e('Wrong Password Message','wpcp');?></b></label><br/>
                    <?php _e('This message is used when user try to enter the password in front-end : When password protection mechanism on.','wpcp');?> 
                </p>
                <p class="form-field">
                    <textarea rows="5" cols="70" name="wpcp_wrongpass_msg" id="wpcp_wrongpass_msg" ><?php echo $wrongPassMsg; ?></textarea>
                </p>
            </div>
            <div class="save-submit">
                <p class="form-field-submit">
                    <input type="submit" class="button submit" name="savemsg" value="Save Changes">
                </p>
            </div>
        </form>  
    </div>
    <!--  ===========================Message body END =============================================  --> 

</div>    