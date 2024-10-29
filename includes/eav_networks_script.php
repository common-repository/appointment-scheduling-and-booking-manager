<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function eav_register_script() {
    wp_register_script( 'eav-clnr-script', plugins_url('/js/eav.js', __FILE__), array('jquery'), '2.5.1' );
    wp_register_script( 'eav-clnr-click', plugins_url('/js/eav_click.js', __FILE__), array('jquery'), '2.5.1' );
    wp_register_script( 'eav-clnr-schedule', plugins_url('/js/eav_schedule.js', __FILE__), array('jquery'), '2.5.1' );
    wp_register_script( 'Card_Validator', plugins_url('/js/cardvalidator.js', __FILE__), array('jquery'), '2.5.1' );
    wp_register_script( 'payment_js', plugins_url('/js/payment.js', __FILE__), array('jquery'), '2.5.1' );
    wp_register_style ( 'clndr',  plugins_url('css/clnr-eav-styles.css',__FILE__ ) );
	wp_register_style ( 'payment',  plugins_url('css/style_payment.css',__FILE__ ) );

}

// use the registered jquery and style above
add_action('wp_enqueue_scripts', 'eav_enqueue_style');

function eav_enqueue_style(){
 wp_enqueue_script('eav-clnr-script');
 wp_enqueue_script('eav-clnr-click');
 wp_enqueue_script('Card_Validator');
 wp_enqueue_script('eav-clnr-schedule');
 wp_enqueue_script('payment_js');
 wp_enqueue_style( 'clndr' );
 wp_enqueue_style( 'payment' );
}
?>