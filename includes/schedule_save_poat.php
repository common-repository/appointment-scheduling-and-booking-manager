<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function eav_schedule_post($u_id,$date,$slot_start,$day ,$month,$year)
{
 $postarr=array(
        'post_author' => $u_id,
        'post_title' => 'Meeting',
        'post_excerpt' => 'Meeting',
        'post_status' => 'schedule',
        'post_type' => 'eav_networks',
               );
 $post_id=wp_insert_post($postarr);
 
 add_post_meta( $post_id,'_date',$date);
 add_post_meta( $post_id,'_slot_start',$slot_start);
 
  eav_scheduling_screen_content($day ,$month,$year);
}

function eav_delete_post($post_id,$day ,$month,$year)
 {
	wp_delete_post($post_id);
	delete_post_meta($post_id,'_date');
	delete_post_meta($post_id,'_slot_start');
	
	eav_scheduling_screen_content($day ,$month,$year);
}