<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $bp;
$user_id=$bp->displayed_user->id;
$email=get_user_meta($user_id,'eav_user_email',true);
$paypal_signature=get_user_meta($user_id,'eav_user_paypal_sign' ,true);
$paypal_username=get_user_meta($user_id,'eav_user_paypal_username' ,true);
$paypal_password=get_user_meta($user_id,'eav_user_paypal_paswd' ,true);

// Set sandbox (test mode) to true/false.
$sandbox = get_option('eav_paypal_setting' , 'TRUE');
 
// Set PayPal API version and credentials.
$api_version = '85.0';
$api_endpoint = $sandbox ? 'https://api-3t.sandbox.paypal.com/nvp' : 'https://api-3t.paypal.com/nvp';
$api_username = $sandbox ? $paypal_username : $paypal_username;
$api_password = $sandbox ? $paypal_password : $paypal_password;
$api_signature = $sandbox ? $paypal_signature : $paypal_signature;

?>