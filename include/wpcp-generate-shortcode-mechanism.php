<?php

/*
 * special functionality.
 */

/*
 * Generate shortcode :[wpcp_content_protection is_logged_in='1' is_user_roles='#enter user roles#'].......content ...........[/wpcp_content_protection]
 */

function wpcp_content_protection_callback($atts, $content) {

    //create session
    session_start();

    extract(shortcode_atts(array(
        'is_logged_in' => 0,
        'is_user_roles' => 0,
        'is_password' => 0
                    ), $atts));
    
    $passwordpt = $atts['is_password'];
    $userroles = $atts['is_user_roles'];
    $login_mechanism = $atts['is_logged_in'];
    
    //function wpcp_content_protection_functions($content,$isloggedin=0,$userroles=0,$ispassword=0);
    $content = wpcp_content_protection_functions($content, $login_mechanism, $userroles, $passwordpt);
    return $content;
}

add_shortcode('wpcp_content_protection', 'wpcp_content_protection_callback');

//****************************** Shortcode functionality end *******************************************//
//****************************** New Feature for Advanced custom field *********************************//
/*
 * How to use : 
 * wpcp_protection_with_advanced_custom_field($acfkey,$isloggedin=1,$userroles,$ispassword)
 */
function wpcp_protection_with_advanced_custom_field($acfkey = 'post_content', $login_mechanism = 0, $userroles = 0, $passwordpt = 0) {
    
    //call the functions.
    $acfcontent = get_post_meta(get_the_ID(),$acfkey,TRUE);
    $content    = wpcp_content_protection_functions($acfcontent, $login_mechanism, $userroles, $passwordpt);
    return $content;
}

//****************************** New Feature for Advanced custom field *********************************//
//functions.
function wpcp_content_protection_functions($content='post_content', $login_mechanism = 0, $userroles = 0, $passwordpt = 0) {

    //start the session.
    @session_start();
    
    //initialize the content section. : if value null:
    if($content == "post_content"){
       $content = get_post_meta(get_the_ID(),'post_content',TRUE); 
    }
    //creating objects of the functions.
    $objFun = new wpcpfunctions();
    $output = array();

    /*
     * Now we will checked, how many and which protection mechanism is active. 
     * if variable have the value = 1 , then it will active otherwise not.
     */
    //A: ****************check login mechanism.
    //$login_mechanism = $atts['is_logged_in'];
    if ($login_mechanism == 1) {
        /*
         * login active : 1 , not login =0
         *  In output : if login active it means no need to show the login mechanism is active 
         *  because it has logged in.
         */
        $loginStatus = $objFun->wpcp_is_user_logged_in();
        if ($loginStatus == 1) {
            $output['is_logged_in'] = 0;
        } else {
            $output['is_logged_in'] = 1;
        }
    }
    //B: ****************User Roles mechanism.  
    //$userroles = $atts['is_user_roles'];
    //if userroles not empty then it is active mechanism i.e we need to check the mechanism.
    /*
     * if user roles empty then no protection is needed to check.
     * but if it has the value it means we need to check the user roles.
     * is_user_roles : 1 => we need to prevent for access the content.
     * is_user_roles : 0 => we allow to access the content. : No protection mechanism on content.
     */
    if (!empty($userroles)) {

        $loggedin_userroles = $objFun->wpcp_get_logged_in_userroles();

        if ("$loggedin_userroles" == "$userroles") {
            $output['is_user_roles'] = 0;
        } else {
            $output['is_user_roles'] = 1;
        }
    } else {
        $output['is_user_roles'] = 0;
    }
    //C: ****************User Roles mechanism.     
    /*
     * Password protection mechanism.
     * passwordpt = 123 or more than 0, mechanism active so we need to protect for accessing the content.
     * passwordpt = 0, mechanims are not active and we allow to access the content.
     */
    //$passwordpt = $atts['is_password'];

        if (!empty($passwordpt) ) {

        /*
         * Adding the session functionality
         * if session password is equal to the password which is set by the admin then we will break this protection and allow
         * to access the content by this mechanism.
         */
        //if form submit
        if (isset($_POST['post_password_shortcode'])) {
            $form_user_password = $_POST['post_password'];
            if ($form_user_password == $passwordpt) {
                $_SESSION["post_password"] = 1;
            } else {
                //display a wrong password message.
                //adding wrong password message in session 
                //it is used to show on frontend.Now recently,it will be used in file :wpcp-content-protection-mechanism.php 

                $password_Str = get_option('wpcp_wrongpass_msg');
                if (empty($password_Str)) {
                    //default message
                    $password_Str = "<p style='color:red'>You have entered the wrong password.</p>";
                }

                $_SESSION['wpcp_enter_wrong_password'] = $password_Str;
            }
        }

        if (isset($_SESSION["post_password"])) {
            if ($_SESSION["post_password"] == 1) {
                $output['is_password'] = 0;
            }
        } else {
            $output['is_password'] = 1;
        }
    } else {
        $output['is_password'] = 0;
    }
    //print_r($_SESSION);
    //******************* END ALL Mechanism *************   
    //***************** Collect the active protection message .
    $message = $objFun->wpcp_get_protection_msg($output, 1);
    /*
     * check if follow all the active mechanism i.e not any error message then show the actual content.
     */
    if (!empty($message)) {
        $content = '<div class="wpcp">' . $message . '</div>';
    }

    return do_shortcode($content);
}

?>