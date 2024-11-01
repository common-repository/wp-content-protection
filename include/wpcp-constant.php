<?php
/* 
 *Constant value : 
 */


//initialization contstant value

define('BLOCKED_POST_TYPE',serialize(array('attachment','shop_order','acf','nav_menu_item','wpcf7_contact_form')));

//initialize message body
$protectionMsg =__("<strong>Before accessing this post content,you need to follow these security.</strong>{wpcp_security_level}","wpcp");
define('SETTING_PROTECTION_MSG',$protectionMsg);

$wrongPassMsg   =__('<p style="color:red;">You have entered the wrong password</p>.',"wpcp");
define('SETTING_WRONGPASS_MSG',$wrongPassMsg);