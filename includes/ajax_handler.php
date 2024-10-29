<?php
function eav_booking_modifications_callback () {
$day=intval($_POST['day']);
$month=intval($_POST['month']);
$year=intval($_POST['year']);
 eav_booking_screen_content($day ,$month,$year);
}
add_action( 'wp_ajax_nopriv_um_cb', 'eav_booking_modifications_callback' );
add_action( 'wp_ajax_um_cb', 'eav_booking_modifications_callback' );

function eav_scheduling_modifications_callback () {
$day=intval($_POST['day']);
$month=intval($_POST['month']);
$year=intval($_POST['year']);
 eav_scheduling_screen_content($day ,$month,$year);
}
add_action( 'wp_ajax_nopriv_eav_cb', 'eav_scheduling_modifications_callback' );
add_action( 'wp_ajax_eav_cb', 'eav_scheduling_modifications_callback' );

function eav_schedule_callback () {
$slot_start=sanitize_text_field($_POST['slot_start']);
$date=sanitize_text_field($_POST['date']);
$u_id=sanitize_text_field($_POST['u_id']);
$day=intval($_POST['day']);
$month=intval($_POST['month']);
$year=intval($_POST['year']);
eav_schedule_post($u_id,$date,$slot_start,$day ,$month,$year);
}
add_action( 'wp_ajax_nopriv_eav_sched', 'eav_schedule_callback' );
add_action( 'wp_ajax_eav_sched', 'eav_schedule_callback' );


function eav_delete_callback () {
$post_id=sanitize_text_field($_POST['post_id']);
$day=intval($_POST['day']);
$month=intval($_POST['month']);
$year=intval($_POST['year']);
eav_delete_post($post_id ,$day ,$month,$year);

}
add_action( 'wp_ajax_nopriv_eav_nonsched', 'eav_delete_callback' );
add_action( 'wp_ajax_eav_nonsched', 'eav_delete_callback' );



function eav_payment_callback () {
eav_booking_update_db_payment();
}
add_action( 'wp_ajax_nopriv_ajax_act', 'eav_payment_callback' );
add_action( 'wp_ajax_ajax_act', 'eav_payment_callback' );


function eav_no_payment_callback () {
eav_booking_update_db_no_payment();
}
add_action( 'wp_ajax_nopriv_ajax_act_no_pay', 'eav_no_payment_callback' );
add_action( 'wp_ajax_ajax_act_no_pay', 'eav_no_payment_callback' );



function eav_Settings_callback () {

if ( 
    ! isset( $_POST['saving_settings_nounce'] ) 
    || ! wp_verify_nonce( $_POST['saving_settings_nounce'], 'saving_settings' ) 
) {

   print 'Sorry, your nonce did not verify.';
   exit;
}
else{
if(isset($_POST['comm_email'])) $email_form =  sanitize_email($_POST['comm_email']);
if(isset($_POST['paypal_signature'])) $paypal_signature_form =  sanitize_text_field($_POST['paypal_signature']);
if(isset($_POST['paypal_username'])) $paypal_username_form =  sanitize_text_field($_POST['paypal_username']);
if(isset($_POST['paypal_paswrd'])) $paypal_password_form =  sanitize_text_field($_POST['paypal_paswrd']);
eav_User_seetings_update($email_form,$paypal_signature_form,$paypal_username_form,$paypal_password_form);
}

}
add_action( 'wp_ajax_ajax_setting_act', 'eav_Settings_callback');
add_action( 'wp_ajax_ajax_setting_act', 'eav_Settings_callback');


function eav_User_seetings_update($user_email,$user_paypal_sign,$user_paypal_name,$user_paypal_pswrd){

$user_id=get_current_user_id();
$email=get_user_meta($user_id,'eav_user_email',true);
$paypal_signature=get_user_meta($user_id,'eav_user_paypal_sign' ,true);
$paypal_username=get_user_meta($user_id,'eav_user_paypal_username' ,true);
$paypal_password=get_user_meta($user_id,'eav_user_paypal_paswd' ,true);

if(!empty($email)){
	update_user_meta( $user_id ,'eav_user_email',$user_email);
	}	
else{
	add_user_meta( $user_id ,'eav_user_email',$user_email);
	}
	
if(!empty($paypal_signature)){
	update_user_meta( $user_id ,'eav_user_paypal_sign',$user_paypal_sign);
	}	
else{
	add_user_meta( $user_id ,'eav_user_paypal_sign',$user_paypal_sign);
	}	
	
if(!empty($paypal_username)){
	update_user_meta( $user_id ,'eav_user_paypal_username',$user_paypal_name);
	}	
else{
	add_user_meta( $user_id ,'eav_user_paypal_username',$user_paypal_name);
	}
	
	
if(!empty($paypal_password)){
	update_user_meta( $user_id ,'eav_user_paypal_paswd',$user_paypal_pswrd);
	}	
else{
	add_user_meta( $user_id ,'eav_user_paypal_paswd',$user_paypal_pswrd);
	}	
	
if(sanitize_text_field($_POST['action']) == 'ajax_setting_act')
{
wp_die();
}
	
}