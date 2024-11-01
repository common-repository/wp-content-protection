<?php
/*
 * WPCP content protections : Functions 
 */
        
class wpcpfunctions {
    /*
     * Get all available post type in a wp website.
     */
function __construct() {
   // if($this->wpcp_session_start_backup()){echo 'session started 1st time recently';}else{echo 'session started phale ka';};
    $this->wpcp_register_session();
}
/*
 * Core functions.
 */

function wpcp_register_session(){
    
    if(!isset($_SESSION)){
        session_start();
    } 
    
}


function wpcp_session_start()
    {
        if (isset($_COOKIE['PHPSESSID'])) {
            $sessid = $_COOKIE['PHPSESSID'];
        } else if (isset($_GET['PHPSESSID'])) {
            $sessid = $_GET['PHPSESSID'];
        } else {
            session_start();
            return false;
        }
       
        if (!preg_match('/^[a-z0-9]{32}$/', $sessid)) {
            return false;
        }
        session_start();
       
        return true;
    }
/*
 * Get available post type which is used for creating an posts.
 */
function wpcp_get_available_post_type() {
        
        $list_posttype =array('page','post');
        
        $custom_post_types = get_post_types( array(
                // Set to FALSE to return only custom post types
                '_builtin' => FALSE,
                // Set to TRUE to return only public post types
                'public' => true
            ) );
        
        if($custom_post_types){
            foreach($custom_post_types as $post_type){
               $list_posttype[]= $post_type;        
            }
        }
      /*  global $wpdb;
        $sql_posttype = "SELECT DISTINCT `post_type` FROM " . $wpdb->prefix . "posts WHERE `post_status`='publish' ";
        $arr_posttype = $wpdb->get_results($sql_posttype);
        return $arr_posttype;*/
        return $list_posttype;
    }

    /*
     * Get all the available roles 
     */

    function wpcp_get_role_names() {
        global $wp_roles;

        if (!isset($wp_roles))
            $wp_roles = new WP_Roles();

        return $wp_roles->get_names();
    }

    /*
     * Get all available active mechanism of this post id
     * output 
     * key : is_user_roles = 1 : active mechanism ,: is_user_roles = 0 :Not active mechanism 
     * key : is_logged_in  = 1 : active mechanism ,: is_logged_in  = 0 :Not active mechanism 
     * key : is_password   = 1 : active mechanism ,: is_password   = 0 :Not active mechanism 
     */

    function wpcp_get_available_protection_mechanism($pid) {

        $output = array();

        $userroles = get_post_meta($pid, 'wpcp_isprotection_userroles', TRUE);
        $loggedin = get_post_meta($pid, 'wpcp_isprotection_loggedin', TRUE);
        $passwordpt = get_post_meta($pid, 'wpcp_isprotection_password', TRUE);

        //if userroles not empty then it is active mechanism i.e we need to check the mechanism.
        /*
         * if user roles empty then no protection is needed to check.
         * but if it has the value it means we need to check the user roles.
         * is_user_roles : 1 => we need to prevent for access the content.
         * is_user_roles : 0 => we allow to access the content. : No protection mechanism on content.
         */
        if (!empty($userroles)) {

            $loggedin_userroles = $this->wpcp_get_logged_in_userroles();
            
            if ("$loggedin_userroles" == "$userroles") {
              $output['is_user_roles'] = 0;
            } else {
              $output['is_user_roles'] = 1;
            }
        } else {
            $output['is_user_roles'] = 0;
        }


        /*
         * if logged in security active then check it is verified or not
         * if not verified then it will display on frontend.
         */
        if ($loggedin == 1) {
            /*
             * it will returns .
             *  login active : 1 , not login =0
             *  In output : if login active it means no need to show the login mechanism is active 
             * because it has logged in.
             */
            $loginStatus = $this->wpcp_is_user_logged_in();
            if ($loginStatus == 1) {
                $output['is_logged_in'] = 0;
            } else {
                $output['is_logged_in'] = 1;
            }
        } else {
            $output['is_logged_in'] = 0;
        }

        /*
         * Password protection mechanism.
         * passwordpt = 1, mechanism active so we need to protect for accessing the content.
         * passwordpt = 0, mechanims are not active and we allow to access the content.
         */
        if($passwordpt == 1){
         
            /*
             * Adding the session functionality
             * if session password is equal to the password which is set by the admin then we will break this protection and allow
             * to access the content by this mechanism.
             * $_SESSION["post_password_$pid"] = 1 : means password which is added by the user is matched.
             * $_SESSION["post_password_$pid"] = 0 : means password which is added by the user not matched.
             */
            
          if(isset($_SESSION["post_password_$pid"])){
              if($_SESSION["post_password_$pid"]==1){
                  $output['is_password']=0;
              }
          }else{
              $output['is_password']=1;
          }
          
            
        }else{
            $output['is_password']=0;
        }
        
        return $output;
    }

    /*
     * check if user logged in.
     */

    function wpcp_is_user_logged_in() {
        $output = 0;
        if (is_user_logged_in()) {
            $output = 1;
        } else {
            $output = 0;
        }
        return $output;
    }

    /*
     * Get logged in user roles .
     */

    function wpcp_get_logged_in_userroles() {
        $output = 0;
        if (is_user_logged_in()) {

            global $current_user;
            $user_roles = $current_user->roles;
            $user_role = array_shift($user_roles);

            $output = $user_role;
        } else {
            $output = 0;
        }
        return $output;
    }
    /*
     * Get protection message which will display for user i.e this post have following security.
     * New Feature: 
     * Password protection mechanism used in two way's
     * 1: By Filter : the_content : 
     * 2: By shortcode 
     * So we can identified it is call by filter or shortcode.
     */
    function wpcp_get_protection_msg($resArr,$is_by_shortcode=0){
        @session_start();//start the session.
        $output ="";
        $loopmsg="";
//        echo '<pre>';
//        print_r($resArr);
//        echo '</pre>';
        /*
         * we need to check the three types of the security array.
         * 1: logged in 
         * 2: user roles
         * 3: Password
         */
        if(count($resArr)>0){
         foreach($resArr as $key=>$value){
             
           if($value == 1){
              
                if($key == "is_user_roles"){
                 $loopmsg .='<li>User Roles Security.</li>';   
                }
                
                if($key == "is_logged_in"){
                $loopmsg .='<li>Loggedin Security.</li>';       
                }
                
                if($key == "is_password"){
               $loopmsg .='<li>Password Security.</li>';     
               //create for with different submit button name: 
                        if($is_by_shortcode == 1){
                            $loopmsg .= $this->wpcp_create_passwordform('post_password_shortcode');
                        }else{
                            $loopmsg .= $this->wpcp_create_passwordform();
                        }
                
               
                }
               
           }  
             
           }
         /*
          * if output have some value i.e it have the security message .
          */  
           if(!empty($loopmsg)){
               
            $active_protection_method_msg ="<ul>";
            $active_protection_method_msg .=$loopmsg;
            $active_protection_method_msg .="</ul>";
               //get message text from setting => wpcontent protection.
               $db_message =get_option('wpcp_content_protection_msg');
               
               if(empty($db_message)){
                   $db_message ='<strong>Before accessing this post content,you need to follow these security.</strong>';
                   $db_message .=$active_protection_method_msg;
               }


                //adding the wrong password message if anyone enter the password and submit.
               //Note: it will not works on first time,when show the protection message. works only if anyone enter the password and hit submit button.
             if(isset($_SESSION['wpcp_enter_wrong_password'])){
                 $db_message =$_SESSION['wpcp_enter_wrong_password'] ."<br/>".$db_message;
                 unset($_SESSION['wpcp_enter_wrong_password']);
                 
             }  
              
              //create an message with active protection mechanism.
              $output .=$this->wpcp_get_replace_msg("{wpcp_security_level}",$active_protection_method_msg,$db_message);
               
//               $output .='<strong>Before accessing this post content,you need to follow these security.</strong>';
//               $output .='<ul>';
//               $output .=$loopmsg;
//               $output .='</ul>';
           }
           
           
        }
        
    return $output;    
    }
    /*
     * Password form : Two ways
     * 1:When this form used by filter: the_content : name:post_password_submit
     * 2:When this form used by shortcode:          : name:post_password_shortcode
     */
  function wpcp_create_passwordform($name='post_password_submit'){
   
      $output ="";
      $output .="<form method='Post' action=''>";
      
      $output .="<input type='hidden' name='post_id' value='".  get_the_ID()."'>";
      $output .="<input type='hidden' name='formOption' value='post_password'>";
      
      $output .="<label for='post_password'>Enter Password: </label>";
      $output .="<input type='password' name='post_password' id='post_password' value='' />&nbsp; ";
      $output .="<input type='submit' name='$name' value='Submit' />";
      $output .="</form>";
      return $output;
  } 
  
  /*
   * search replace functionality
   */
    function wpcp_get_replace_msg($search, $replace, $string) {

        return str_replace($search, $replace, $string);
    }
  
    //Below class end
}

new wpcpfunctions();?>
