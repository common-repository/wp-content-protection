<?php
/*
 * Now adding the filter mechanism in which we will follow these three conditions
 * 1:  Access only logged in user
 * 2:  Make this password protection
 * 3:  Make this user roles protection
 */
//***************************************** When password form submit from frontend 
/*
 * File Resource : include/wpcp-functions.php
 * When password security active and user enter the password.
 */
if (isset($_POST['post_password_submit'])) {

    $currpostid   = intval($_POST['post_id']);
    $db_passvalue = get_post_meta($currpostid, 'wpcp_isprotection_passvalue', TRUE);
    $user_passvalue= sanitize_text_field($_POST['post_password']);

    if ($db_passvalue == $user_passvalue) {
        //generate the cookies for verify.
        $_SESSION["post_password_$currpostid"]=1;
   }else{
      //display a wrong password message.
      //adding wrong password message in session 
       //it is used to show on frontend.Now recently,it will be used in file :wpcp-content-protection-mechanism.php 
        
        $password_Str = get_option('wpcp_wrongpass_msg');
        if(empty($password_Str)){
            //default message
            $password_Str ="<p style='color:red'>You have entered the wrong password.</p>";
        }
        
        $_SESSION['wpcp_enter_wrong_password']=$password_Str;    
   }
   
    wp_redirect(get_permalink($currpostid));
    exit;
}
//***************************************** When password form submit from frontend 
/*
 * Start the work :
 * :1: Add filter in " the_content" 
 */
add_filter('the_content', 'wpcp_the_content_filter');

// returns the content of $GLOBALS['post']
// if the page is called 'debug'
function wpcp_the_content_filter($content) {

     //create the object of the functions.
         $objFun  = new wpcpfunctions();
    
    //Get current content i.e page,post,cpt id which is landing.
        $currentID = $GLOBALS['post']->ID;
    /*
     * Get how many active protections of this post id
     * In protection mechanism,
     * if value =0 , no need for protection i.e allow to access the content.
     * if value =1 , need for protection and don't allow to access it.
     */
    $avail_prot_mech= $objFun->wpcp_get_available_protection_mechanism($currentID);
     //check if protection mechanism active.
    if(in_array(1,$avail_prot_mech)){
     $message = $objFun->wpcp_get_protection_msg($avail_prot_mech);
     $content = '<div class="wpcp">'.$message.'</div>';
    }
    return $content;
} ?>