/* 
 *wpcp scripts file
 */
/* 
 Created on : 19 Dec, 2014, 8:30:09 PM
 Author     : shankaranand
 */
jQuery(document).ready(function($) {


    //when click on the : Make this password protection :
    $('.password-protected').click(function() {

        if ($('input.checkbox_check').is(':checked')) {
            $('.form-password-field').css('display', 'inline-block');

        } else {
            $('.form-password-field').css('display', 'none');
        }

    });
//END : when click on the : Make this password protection :





});//end of the dom ready